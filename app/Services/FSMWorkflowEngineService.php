<?php

namespace App\Services;

use App\Exceptions\IllegalTransitionException;
use App\Models\Document;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\DB;

/**
 * Service Utama FSM Engine (Finite State Machine) Laravel 11
 * Model Ramping 6-Status Sesuai Konteks Organisasi Universitas Parna Raya (UNIPAR)
 * 
 * Mengendalikan siklus hidup dokumen kerja sama (MoU, MoA, IA, Paket Terpadu),
 * menegakkan Anti-Bypass Guard, Role-Based Access Control (RBAC),
 * serta pencatatan jejak audit relasional permanen di MySQL.
 */
class FSMWorkflowEngineService
{
    /**
     * Matriks Transisi Status Formal FSM: 6 Status Diskret & 8 Aturan Transisi
     * Menyimpan target status, wewenang peran (RBAC), label tombol, ikon, dan styling.
     */
    protected array $matrix = [
        'DRAFT' => [
            'SUBMIT' => [
                'to' => 'REVIEW',
                'roles' => ['UNIT', 'MASTER'],
                'label' => 'Ajukan Naskah ke Meja Pemeriksaan (SUBMIT)',
                'icon' => 'bi-send-fill',
                'btn' => 'btn-dark'
            ],
            'CANCEL' => [
                'to' => 'CANCELLED',
                'roles' => ['UNIT', 'MASTER'],
                'label' => 'Batalkan Pengusulan Naskah (CANCEL)',
                'icon' => 'bi-x-octagon',
                'btn' => 'btn-outline-danger'
            ]
        ],
        'REVIEW' => [
            'ACTIVATE' => [
                'to' => 'ACTIVE',
                'roles' => ['LEADER', 'LEGAL', 'MASTER'],
                'label' => 'Sahkan Naskah & Aktifkan (Teken Basah + Scan)',
                'icon' => 'bi-patch-check-fill',
                'btn' => 'btn-success'
            ],
            'RETURN_TO_DRAFT' => [
                'to' => 'DRAFT',
                'roles' => ['LEADER', 'LEGAL', 'MASTER'],
                'label' => 'Kembalikan ke Pengusul (Ditolak / Perlu Perbaikan)',
                'icon' => 'bi-arrow-return-left',
                'btn' => 'btn-warning text-dark'
            ]
        ],
        'ACTIVE' => [
            'TRIGGER_WARNING' => [
                'to' => 'WARNING',
                'roles' => ['SYSTEM', 'MASTER'],
                'label' => 'Picu Peringatan Dini Masa Berlaku (H-90)',
                'icon' => 'bi-bell-fill',
                'btn' => 'btn-warning text-dark'
            ]
        ],
        'WARNING' => [
            'RENEW' => [
                'to' => 'ACTIVE',
                'roles' => ['UNIT', 'LEADER', 'LEGAL', 'MASTER'],
                'label' => 'Perpanjang Naskah (Inisiasi Adendum)',
                'icon' => 'bi-arrow-clockwise',
                'btn' => 'btn-primary'
            ],
            'EXPIRE' => [
                'to' => 'EXPIRED',
                'roles' => ['SYSTEM', 'MASTER'],
                'label' => 'Catat Naskah Telah Kedaluwarsa',
                'icon' => 'bi-clock-history',
                'btn' => 'btn-danger'
            ]
        ],
        'EXPIRED' => [
            'RENEW' => [
                'to' => 'ACTIVE',
                'roles' => ['UNIT', 'LEADER', 'LEGAL', 'MASTER'],
                'label' => 'Reaktivasi Naskah Kedaluwarsa',
                'icon' => 'bi-arrow-clockwise',
                'btn' => 'btn-dark'
            ]
        ],
        'CANCELLED' => []
    ];

    /**
     * Dapatkan seluruh matriks transisi FSM (untuk keperluan visualisasi tabel/UI)
     */
    public function getFullMatrix(): array
    {
        return $this->matrix;
    }

    /**
     * Dapatkan daftar event yang tersedia untuk status saat ini
     */
    public function getAvailableEvents(string $currentStatus): array
    {
        return $this->matrix[$currentStatus] ?? [];
    }

    /**
     * Eksekusi Transisi Status FSM Formal
     *
     * @throws IllegalTransitionException
     */
    public function transition(Document $document, string $event, string $actorRole, string $actorName, ?string $notes = null, array $additionalData = []): Document
    {
        $currentStatus = $document->status;

        // 1. Validasi Keabsahan Transisi Status (Anti-Bypass Guard)
        if (!isset($this->matrix[$currentStatus][$event])) {
            throw new IllegalTransitionException(
                "Pelanggaran Alur FSM: Event [{$event}] tidak sah dieksekusi pada status [{$currentStatus}]!"
            );
        }

        $conf = $this->matrix[$currentStatus][$event];
        $newStatus = $conf['to'];
        $allowedRoles = $conf['roles'];

        // 2. Validasi Hak Akses Peran (RBAC Guard)
        if ($actorRole !== 'MASTER' && $actorRole !== 'ADMIN' && !in_array($actorRole, $allowedRoles, true)) {
            $allowedStr = implode(', ', $allowedRoles);
            throw new IllegalTransitionException(
                "Pelanggaran RBAC: Peran [{$actorRole}] tidak berwenang mengeksekusi event [{$event}]. Wewenang aksi ini: [{$allowedStr}]."
            );
        }

        // 3. Eksekusi Perubahan Status & Pencatatan Audit Trail Relasional (Database Transaction)
        return DB::transaction(function () use ($document, $event, $currentStatus, $newStatus, $actorRole, $actorName, $notes, $additionalData) {
            $document->status = $newStatus;

            // Simpan nomor pihak pertama (UNIPAR) jika diperbarui / perpanjangan
            if (!empty($additionalData['doc_number'])) {
                $document->doc_number = $additionalData['doc_number'];
            }

            // Simpan nomor pihak kedua (mitra) jika dikirimkan
            if (!empty($additionalData['partner_doc_number'])) {
                $document->partner_doc_number = $additionalData['partner_doc_number'];
            }

            // Simpan berkas scan PDF jika diunggah baru
            if (!empty($additionalData['signed_file'])) {
                $document->signed_file = $additionalData['signed_file'];
            }

            // Simpan masa berlaku baru jika dikirimkan (misal perpanjangan adendum)
            if (!empty($additionalData['validity'])) {
                $document->validity = $additionalData['validity'];
            }

            // Jika masuk status ACTIVE: set tanggal tanda tangan & catat scan fisik
            if ($newStatus === 'ACTIVE') {
                if (empty($document->date_approved)) {
                    $document->date_approved = now()->toDateString();
                }
                if (empty($document->date_signed) || !empty($additionalData['date_signed'])) {
                    $document->date_signed = $additionalData['date_signed'] ?? now()->toDateString();
                }
                if (empty($document->signed_file)) {
                    $document->signed_file = 'signed_scan_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '.pdf';
                }
            }

            $document->save();

            // Susun catatan audit trail otomatis yang informatif
            $auditNote = $notes;
            if (!$auditNote) {
                if ($newStatus === 'ACTIVE') {
                    $auditNote = 'Naskah sah bertanda tangan basah & bermeterai aktif. Nomor Pihak I (UNIPAR): ' . ($document->doc_number ?: $document->doc_id) . (!empty($document->partner_doc_number) ? ', Nomor Pihak II (Mitra): ' . $document->partner_doc_number : '');
                } elseif ($newStatus === 'REVIEW') {
                    $auditNote = 'Naskah diajukan ke meja pemeriksaan pimpinan / biro kerja sama';
                } elseif ($newStatus === 'DRAFT' && $event === 'RETURN_TO_DRAFT') {
                    $auditNote = 'Naskah dikembalikan oleh pimpinan ke status DRAFT untuk diperbaiki klausulnya';
                } elseif ($newStatus === 'CANCELLED') {
                    $auditNote = 'Pengusulan naskah dibatalkan secara permanen oleh pengusul';
                } elseif ($newStatus === 'WARNING') {
                    $auditNote = 'Peringatan dini masa berlaku H-90 terpicu otomatis oleh sistem';
                } elseif ($newStatus === 'EXPIRED') {
                    $auditNote = 'Masa berlaku naskah telah berakhir secara alami';
                } else {
                    $auditNote = 'Transisi status dieksekusi oleh pejabat berwenang';
                }
            }

            // Catat ke tabel audit_trails permanen
            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => $actorName,
                'role' => $actorRole,
                'event' => $event,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
                'notes' => $auditNote,
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);

            return $document;
        });
    }
}
