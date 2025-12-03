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
    const PREFIX_SET = 'Z';

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
        // Normalize type naming to match Livewire components expectations
        if ($type === 'set') {
            $type = 'equipment_set';
        }
        $entity = $this->findEntity($barcode, $type);

        return [
            'type' => $type,
            'entity' => $entity,
            'id' => $entity?->id,
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
            self::PREFIX_SET => 'set',
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
            'equipment_set' => \App\Models\EquipmentSet::where('barcode', $barcode)->first(),
            default => null,
        };
    }

    /**
     * Get suggestions for a partial input across all types.
     * Returns an array of up to 10 suggestions with barcode, type, id, name.
     *
     * @param string $input
     * @param string $scope 'all'|'students'|'equipment'|'equipment_sets'
     * @return array<int, array{barcode:string,type:string,id:int,name:string}>
     */
    public function getSuggestions(string $input, string $scope = 'all'): array
    {
        $term = trim($input);
        if ($term === '') {
            return [];
        }

        $suggestions = [];

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        if ($scope === 'all' || $scope === 'students') {
            $users = User::query()
                ->where(function($q) use ($like) {
                    $q->where('name', 'like', $like)
                      ->orWhere('email', 'like', $like)
                      ->orWhere('barcode', 'like', $like);
                })
                ->limit(5)
                ->get(['id','name','barcode']);
            foreach ($users as $u) {
                $suggestions[] = [
                    'barcode' => $u->barcode,
                    'type' => 'student',
                    'id' => $u->id,
                    'name' => $u->name,
                ];
            }
        }

        if ($scope === 'all' || $scope === 'equipment') {
            $equip = Equipment::query()
                ->where(function($q) use ($like) {
                    $q->where('name', 'like', $like)
                      ->orWhere('barcode', 'like', $like)
                      ->orWhere('model', 'like', $like);
                })
                ->limit(5)
                ->get(['id','name','barcode']);
            foreach ($equip as $e) {
                $suggestions[] = [
                    'barcode' => $e->barcode,
                    'type' => 'equipment',
                    'id' => $e->id,
                    'name' => $e->name,
                ];
            }
        }

        if ($scope === 'all' || $scope === 'equipment_sets') {
            $sets = \App\Models\EquipmentSet::query()
                ->where(function($q) use ($like) {
                    $q->where('name', 'like', $like)
                      ->orWhere('barcode', 'like', $like);
                })
                ->limit(5)
                ->get(['id','name','barcode']);
            foreach ($sets as $s) {
                $suggestions[] = [
                    'barcode' => $s->barcode,
                    'type' => 'equipment_set',
                    'id' => $s->id,
                    'name' => $s->name,
                ];
            }
        }

        // Limit total suggestions
        return array_slice($suggestions, 0, 10);
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
        if (preg_match('/^[SEZ]\d{10}$/', $barcode)) {
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
        return self::PREFIX_STUDENT . str_pad((string) $id, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate barcode for equipment
     *
     * @param int $id
     * @return string
     */
    public static function generateEquipmentBarcode(int $id): string
    {
        return self::PREFIX_EQUIPMENT . str_pad((string) $id, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate barcode for equipment set
     *
     * @param int $id
     * @return string
     */
    public static function generateSetBarcode(int $id): string
    {
        return self::PREFIX_SET . str_pad((string) $id, 10, '0', STR_PAD_LEFT);
    }
}
