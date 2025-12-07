<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'course_unit_id',
        'star_type',
        'assigned_by_id',
        'notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Relacja do użytkownika (ucznia), który otrzymał gwiazdkę
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacja do jednostki kursu
     */
    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    /**
     * Relacja do instruktora, który przyznał gwiazdkę
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    /**
     * Oblicza typ biretu (czapki) dla danego użytkownika
     * na podstawie większości gwiazdek (bronze/silver/gold)
     * 
     * @param int $userId
     * @return string|null 'bronze', 'silver', 'gold' lub null jeśli brak
     */
    public static function calculateBiretType(int $userId): ?string
    {
        // Pobierz wszystkie wymagane jednostki kursu
        $requiredUnits = CourseUnit::where('is_required', true)->pluck('id');

        // Pobierz wszystkie gwiazdki dla użytkownika dla wymaganych jednostek
        $achievements = self::where('user_id', $userId)
            ->whereIn('course_unit_id', $requiredUnits)
            ->get();

        // Jeśli użytkownik nie ma gwiazdek dla wszystkich wymaganych jednostek - brak biretu
        if ($achievements->count() < $requiredUnits->count()) {
            return null;
        }

        // Policz gwiazdki wg typu
        $counts = [
            'bronze' => $achievements->where('star_type', 'bronze')->count(),
            'silver' => $achievements->where('star_type', 'silver')->count(),
            'gold' => $achievements->where('star_type', 'gold')->count(),
        ];

        // Znajdź typ z największą liczbą gwiazdek
        arsort($counts);
        $dominantType = array_key_first($counts);

        // Jeśli jest remis między typami, priorytet: gold > silver > bronze
        if ($counts['gold'] === $counts['silver'] && $counts['gold'] > 0) {
            return 'gold';
        }
        if ($counts['silver'] === $counts['bronze'] && $counts['silver'] > 0) {
            return 'silver';
        }

        return $dominantType;
    }

    /**
     * Pomocnicza metoda do sprawdzenia, czy użytkownik może otrzymać biret
     * 
     * @param int $userId
     * @return bool
     */
    public static function canReceiveBiret(int $userId): bool
    {
        return self::calculateBiretType($userId) !== null;
    }

    /**
     * Zwraca przewidywany typ biretu na podstawie obecnych gwiazdek
     * (nie wymaga kompletności wszystkich wymaganych jednostek)
     * 
     * @param int $userId
     * @return string|null
     */
    public static function predictBiretType(int $userId): ?string
    {
        $requiredUnits = CourseUnit::where('is_required', true)->pluck('id');
        
        $achievements = self::where('user_id', $userId)
            ->whereIn('course_unit_id', $requiredUnits)
            ->get();

        if ($achievements->isEmpty()) {
            return null;
        }

        $counts = [
            'bronze' => $achievements->where('star_type', 'bronze')->count(),
            'silver' => $achievements->where('star_type', 'silver')->count(),
            'gold' => $achievements->where('star_type', 'gold')->count(),
        ];

        arsort($counts);
        $dominantType = array_key_first($counts);

        if ($counts['gold'] === $counts['silver'] && $counts['gold'] > 0) {
            return 'gold';
        }
        if ($counts['silver'] === $counts['bronze'] && $counts['silver'] > 0) {
            return 'silver';
        }

        return $dominantType;
    }
}
