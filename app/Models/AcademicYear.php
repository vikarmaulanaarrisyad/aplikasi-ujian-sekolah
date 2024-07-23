<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    // Constants for status values
    const STATUS_ONGOING = 'Sedang Berlangsung';
    const STATUS_COMPLETED = 'Telah Berakhir';
    const STATUS_PENDING = 'Belum Terlaksana';

    /**
     * Get the color class and icon for the status.
     *
     * @return array
     */
    public function statusDetails()
    {
        $details = [
            'color' => 'secondary', // Default color
            'icon' => 'fas fa-question-circle' // Default icon
        ];

        switch ($this->status) {
            case self::STATUS_ONGOING:
                $details['color'] = 'success';
                $details['icon'] = 'fas fa-check-circle'; // Check icon
                break;
            case self::STATUS_COMPLETED:
                $details['color'] = 'danger';
                $details['icon'] = 'fas fa-times-circle'; // Cross icon
                break;
            case self::STATUS_PENDING:
                $details['color'] = 'warning';
                $details['icon'] = 'fas fa-minus-circle'; // Minus icon
                break;
        }

        return $details;
    }

    /**
     * Get the HTML badge for the status with icon.
     *
     * @return string
     */
    public function statusBadge()
    {
        $details = $this->statusDetails();
        return '<span class="badge badge-' . $details['color'] . '"><i class="' . $details['icon'] . '"></i> ' . $this->status . '</span>';
    }
}
