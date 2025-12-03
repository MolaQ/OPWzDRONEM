<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Models\EquipmentNote;
use App\Models\Rental;
use App\Models\RentalGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RentalService
{
    /**
     * Rent equipment or set to user(s)
     *
     * @param array $userIds Array of user IDs (borrowers)
     * @param string $itemBarcode Barcode of equipment or set (E... or Z...)
     * @param int $processedByUserId ID of admin/instructor processing rental
     * @param string|null $notes Optional rental notes
     * @return array ['success' => bool, 'message' => string, 'rental' => Rental|null]
     */
    public function rentItem(array $userIds, string $itemBarcode, int $processedByUserId, ?string $notes = null): array
    {
        try {
            DB::beginTransaction();

            // Validate users exist
            $users = User::whereIn('id', $userIds)->get();
            if ($users->count() !== count($userIds)) {
                return ['success' => false, 'message' => 'Niektórzy użytkownicy nie istnieją', 'rental' => null];
            }

            // Detect item type
            $prefix = substr($itemBarcode, 0, 1);

            if ($prefix === 'E') {
                $equipment = Equipment::where('barcode', $itemBarcode)->first();
                if (!$equipment) {
                    return ['success' => false, 'message' => 'Sprzęt nie istnieje', 'rental' => null];
                }

                if (!$equipment->isAvailableForRental()) {
                    return ['success' => false, 'message' => 'Sprzęt niedostępny: ' . $equipment->status_label, 'rental' => null];
                }

                // Create rental group
                $rentalGroup = RentalGroup::create([
                    'name' => RentalGroup::generateName($userIds),
                ]);
                $rentalGroup->members()->attach($userIds);

                // Create rental
                $rental = Rental::create([
                    'rental_group_id' => $rentalGroup->id,
                    'equipment_id' => $equipment->id,
                    'equipment_set_id' => null,
                    'rented_at' => now(),
                    'rented_by_user_id' => $processedByUserId,
                    'rental_notes' => $notes,
                ]);

                // Update equipment status
                $equipment->update(['status' => 'wypozyczony']);

                DB::commit();
                return ['success' => true, 'message' => 'Sprzęt wypożyczony', 'rental' => $rental];

            } elseif ($prefix === 'Z') {
                $set = EquipmentSet::where('barcode', $itemBarcode)->first();
                if (!$set) {
                    return ['success' => false, 'message' => 'Zestaw nie istnieje', 'rental' => null];
                }

                if (!$set->isAvailable()) {
                    $missing = $set->missingEquipment();
                    if ($missing->isNotEmpty()) {
                        $names = $missing->pluck('name')->join(', ');
                        return ['success' => false, 'message' => 'Zestaw niekompletny. Brakuje: ' . $names, 'rental' => null];
                    }
                    return ['success' => false, 'message' => 'Zestaw niedostępny', 'rental' => null];
                }

                // Create rental group
                $rentalGroup = RentalGroup::create([
                    'name' => RentalGroup::generateName($userIds),
                ]);
                $rentalGroup->members()->attach($userIds);

                // Create rental
                $rental = Rental::create([
                    'rental_group_id' => $rentalGroup->id,
                    'equipment_id' => null,
                    'equipment_set_id' => $set->id,
                    'rented_at' => now(),
                    'rented_by_user_id' => $processedByUserId,
                    'rental_notes' => $notes,
                ]);

                // Update all equipment in set to 'wypozyczony'
                $set->equipments()->update(['status' => 'wypozyczony']);

                DB::commit();
                return ['success' => true, 'message' => 'Zestaw wypożyczony', 'rental' => $rental];
            }

            return ['success' => false, 'message' => 'Nieprawidłowy kod', 'rental' => null];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Błąd: ' . $e->getMessage(), 'rental' => null];
        }
    }

    /**
     * Return equipment or set
     *
     * @param string $itemBarcode Barcode of equipment or set being returned
     * @param int $processedByUserId ID of admin/instructor processing return
     * @param string|null $returnNotes Optional return notes (damage, etc.)
     * @param string $noteType Type of note: 'info', 'warning', 'damage', 'maintenance'
     * @return array ['success' => bool, 'message' => string]
     */
    public function returnItem(string $itemBarcode, int $processedByUserId, ?string $returnNotes = null, string $noteType = 'info'): array
    {
        try {
            DB::beginTransaction();

            $prefix = substr($itemBarcode, 0, 1);

            if ($prefix === 'E') {
                $equipment = Equipment::where('barcode', $itemBarcode)->first();
                if (!$equipment) {
                    return ['success' => false, 'message' => 'Sprzęt nie istnieje'];
                }

                // Find active rental
                $rental = Rental::where('equipment_id', $equipment->id)
                    ->whereNull('returned_at')
                    ->first();

                if (!$rental) {
                    return ['success' => false, 'message' => 'Brak aktywnego wypożyczenia tego sprzętu'];
                }

                // Mark as returned
                $rental->update([
                    'returned_at' => now(),
                    'returned_by_user_id' => $processedByUserId,
                    'return_notes' => $returnNotes ?? 'Brak uwag',
                ]);

                // Update equipment status
                $equipment->update(['status' => 'dostepny']);

                // Add equipment note if there are issues
                if ($returnNotes && $returnNotes !== 'Brak uwag') {
                    EquipmentNote::create([
                        'equipment_id' => $equipment->id,
                        'rental_id' => $rental->id,
                        'note' => $returnNotes,
                        'type' => $noteType,
                        'created_by_user_id' => $processedByUserId,
                    ]);
                }

                DB::commit();
                return ['success' => true, 'message' => 'Sprzęt zwrócony'];

            } elseif ($prefix === 'Z') {
                $set = EquipmentSet::where('barcode', $itemBarcode)->first();
                if (!$set) {
                    return ['success' => false, 'message' => 'Zestaw nie istnieje'];
                }

                // Find active rental
                $rental = Rental::where('equipment_set_id', $set->id)
                    ->whereNull('returned_at')
                    ->first();

                if (!$rental) {
                    return ['success' => false, 'message' => 'Brak aktywnego wypożyczenia tego zestawu'];
                }

                // Mark as returned
                $rental->update([
                    'returned_at' => now(),
                    'returned_by_user_id' => $processedByUserId,
                    'return_notes' => $returnNotes ?? 'Brak uwag',
                ]);

                // Update all equipment in set to 'dostepny'
                $set->equipments()->update(['status' => 'dostepny']);

                // Add notes to each equipment if there are issues
                if ($returnNotes && $returnNotes !== 'Brak uwag') {
                    foreach ($set->equipments as $equipment) {
                        EquipmentNote::create([
                            'equipment_id' => $equipment->id,
                            'rental_id' => $rental->id,
                            'note' => 'Zwrot zestawu: ' . $returnNotes,
                            'type' => $noteType,
                            'created_by_user_id' => $processedByUserId,
                        ]);
                    }
                }

                DB::commit();
                return ['success' => true, 'message' => 'Zestaw zwrócony'];
            }

            return ['success' => false, 'message' => 'Nieprawidłowy kod'];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Błąd: ' . $e->getMessage()];
        }
    }

    /**
     * Check if equipment can be rented (not in 'konserwacja' or 'uszkodzony')
     */
    public function canRent(string $itemBarcode): array
    {
        $prefix = substr($itemBarcode, 0, 1);

        if ($prefix === 'E') {
            $equipment = Equipment::where('barcode', $itemBarcode)->first();
            if (!$equipment) {
                return ['can_rent' => false, 'message' => 'Sprzęt nie istnieje'];
            }

            if ($equipment->status === 'konserwacja') {
                return ['can_rent' => false, 'message' => 'Sprzęt w konserwacji'];
            }

            if ($equipment->status === 'uszkodzony') {
                return ['can_rent' => false, 'message' => 'Sprzęt uszkodzony'];
            }

            if ($equipment->status === 'wypozyczony') {
                return ['can_rent' => false, 'message' => 'Sprzęt już wypożyczony'];
            }

            return ['can_rent' => true, 'message' => 'Dostępny'];

        } elseif ($prefix === 'Z') {
            $set = EquipmentSet::where('barcode', $itemBarcode)->first();
            if (!$set) {
                return ['can_rent' => false, 'message' => 'Zestaw nie istnieje'];
            }

            if (!$set->active) {
                return ['can_rent' => false, 'message' => 'Zestaw nieaktywny'];
            }

            if (!$set->isComplete()) {
                $missing = $set->missingEquipment()->pluck('name')->join(', ');
                return ['can_rent' => false, 'message' => 'Zestaw niekompletny. Brakuje: ' . $missing];
            }

            return ['can_rent' => true, 'message' => 'Zestaw kompletny i dostępny'];
        }

        return ['can_rent' => false, 'message' => 'Nieprawidłowy kod'];
    }
}
