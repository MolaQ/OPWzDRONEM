<?php

namespace App\Services;

use App\Models\User;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;

class BarcodeResolver
{
    /**
     * Prefixes for different entity types
     */
    const PREFIX_STUDENT = 'S';
    const PREFIX_EQUIPMENT = 'E';

    /**
     * Resolve barcode to entity
     * 
     * @param string $barcode
     * @return array{type: string, entity: Model|null, found: bool}
     */
    public function resolve(string $barcode): array
    {
        $barcode = strtoupper(trim($barcode));
        
        if (empty($barcode)) {
            return [
                'type' => 'unknown',
                'entity' => null,
                'found' => false,
            ];
        }

        $type = $this->detectType($barcode);
        $entity = $this->findEntity($barcode, $type);

        return [
            'type' => $type,
            'entity' => $entity,
            'found' => $entity !== null,
        ];
    }

    /**
     * Detect entity type from barcode prefix
     * 
     * @param string $barcode
     * @return string
     */
    protected function detectType(string $barcode): string
    {
        $prefix = substr($barcode, 0, 1);

        return match($prefix) {
            self::PREFIX_STUDENT => 'student',
            self::PREFIX_EQUIPMENT => 'equipment',
            default => 'unknown',
        };
    }

    /**
     * Find entity by barcode and type
     * 
     * @param string $barcode
     * @param string $type
     * @return Model|null
     */
    protected function findEntity(string $barcode, string $type): ?Model
    {
        return match($type) {
            'student' => User::where('barcode', $barcode)->first(),
            'equipment' => Equipment::where('barcode', $barcode)->first(),
            default => null,
        };
    }

    /**
     * Check if barcode format is valid
     * 
     * @param string $barcode
     * @return bool
     */
    public function isValidFormat(string $barcode): bool
    {
        $barcode = strtoupper(trim($barcode));
        
        // Check if starts with valid prefix and has numeric part
        if (preg_match('/^[SE]\d{3,}$/', $barcode)) {
            return true;
        }

        return false;
    }

    /**
     * Generate barcode for student
     * 
     * @param int $id
     * @return string
     */
    public static function generateStudentBarcode(int $id): string
    {
        return self::PREFIX_STUDENT . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate barcode for equipment
     * 
     * @param int $id
     * @return string
     */
    public static function generateEquipmentBarcode(int $id): string
    {
        return self::PREFIX_EQUIPMENT . str_pad($id, 5, '0', STR_PAD_LEFT);
    }
}
