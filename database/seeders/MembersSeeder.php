<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Comment;
use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Services\BarcodeResolver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\PostReaction::truncate();
        \App\Models\Comment::truncate();
        \App\Models\Post::truncate();
        DB::table('equipment_set_items')->truncate();
        EquipmentSet::truncate();
        Equipment::truncate();
        User::truncate();
        Group::truncate();
        DB::table('group_supervisors')->truncate();
        DB::table('group_instructors')->truncate();
        Schema::enableForeignKeyConstraints();

        // Tworzymy grupy uczniowskie
        $group4OPW1a = Group::create([
            'name' => '4OPW1',
            'description' => 'Klasa przygotowania wojskowego - 14 uczniów',
            'active' => true,
        ]);

        $group4OPW1b = Group::create([
            'name' => '4OPW2',
            'description' => 'Klasa przygotowania wojskowego - 12 uczniów',
            'active' => true,
        ]);

        $group5CM = Group::create([
            'name' => '5CM',
            'description' => 'Klasa CyberMIL - 3 uczniów',
            'active' => true,
        ]);

        $group3OPW1 = Group::create([
            'name' => '3OPW1',
            'description' => 'Klasa przygotowania wojskowego - 1 uczeń',
            'active' => true,
        ]);

        // 1 Administrator
        $admin = User::create([
            'name' => 'Administrator Systemu',
            'email' => 'admin@opwzdronem.pl',
            'password' => Hash::make('P@ssw0rd'),
            'pilot_license' => 'PL-ADMIN-001',
            'operator_license' => 'OP-ADMIN-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $admin->assignRole('admin');
        $admin->update(['barcode' => BarcodeResolver::generateStudentBarcode($admin->id)]);

        // Koordynator (global)
        $coordinator = User::create([
            'name' => 'Bogusław Kaczmarek',
            'email' => 'boguslaw.kaczmarek@example.com',
            'password' => Hash::make('Haslo1234'),
            'pilot_license' => 'PL-KOOR-001',
            'operator_license' => 'OP-KOOR-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $coordinator->assignRole('coordinator');
        $coordinator->update(['barcode' => BarcodeResolver::generateStudentBarcode($coordinator->id)]);

        // Dyrektor (global)
        $director = User::create([
            'name' => 'Daria Szostak',
            'email' => 'daria.szostak@example.com',
            'password' => Hash::make('Haslo1234'),
            'pilot_license' => 'PL-DYR-001',
            'operator_license' => 'OP-DYR-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $director->assignRole('director');
        $director->update(['barcode' => BarcodeResolver::generateStudentBarcode($director->id)]);

        // Instruktorzy (mogą być też wychowawcami)
        $instructor1 = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan.kowalski@example.com',
            'password' => Hash::make('Haslo1234'),
            'pilot_license' => 'PL-INST-001',
            'operator_license' => 'OP-INST-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $instructor1->assignRole('instructor');
        $instructor1->update(['barcode' => BarcodeResolver::generateStudentBarcode($instructor1->id)]);

        $instructor2 = User::create([
            'name' => 'Anna Nowak',
            'email' => 'anna.nowak@example.com',
            'password' => Hash::make('Haslo1234'),
            'pilot_license' => 'PL-INST-002',
            'operator_license' => 'OP-INST-002',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $instructor2->assignRole('instructor');
        $instructor2->update(['barcode' => BarcodeResolver::generateStudentBarcode($instructor2->id)]);
        // Wychowawcy (tu przypisujemy istniejących instruktorów jako wychowawców)
        $supervisor1 = $instructor1; // dla 4OPW1 (14 uczniów)
        $supervisor1->assignRole('wychowawca');
        $supervisor2 = $instructor2; // dla 4OPW1 (12 uczniów)
        $supervisor2->assignRole('wychowawca');

        // Możemy mieć jeszcze jednego wychowawcę/instruktora
        $instructor3 = User::create([
            'name' => 'Karol Mazur',
            'email' => 'karol.mazur@example.com',
            'password' => Hash::make('Haslo1234'),
            'pilot_license' => 'PL-INST-003',
            'operator_license' => 'OP-INST-003',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => null,
        ]);
        $instructor3->assignRole('instructor');
        $instructor3->assignRole('wychowawca');
        $instructor3->update(['barcode' => BarcodeResolver::generateStudentBarcode($instructor3->id)]);

        // Studenci w grupach
        $students = collect();

        $students = $students->merge($this->seedStudentsForGroup($group4OPW1a, 14));
        $students = $students->merge($this->seedStudentsForGroup($group4OPW1b, 12));
        $students = $students->merge($this->seedStudentsForGroup($group5CM, 3));
        $students = $students->merge($this->seedStudentsForGroup($group3OPW1, 1));

        // Przypisujemy wychowawców do grup (pivot)
        $group4OPW1a->supervisors()->attach($supervisor1->id);
        $group4OPW1b->supervisors()->attach($supervisor2->id);
        $group5CM->supervisors()->attach($instructor3->id);
        // Grupa 3OPW1 bez wychowawcy (dozwolone)

        // Przypisujemy instruktorów do grup (pivot) - min 1, max 5
        $group4OPW1a->instructors()->attach([$instructor1->id, $instructor2->id]);
        $group4OPW1b->instructors()->attach([$instructor2->id, $instructor3->id]);
        $group5CM->instructors()->attach([$instructor3->id]);
        $group3OPW1->instructors()->attach([$instructor1->id]);

        // Zbieramy wszystkich użytkowników do losowania treści
        $allUsers = collect([$admin, $coordinator, $director, $instructor1, $instructor2, $instructor3])
            ->merge($students);

        // 20 postów
        $posts = [];
        $authors = collect([$admin, $instructor1, $instructor2, $instructor3, $coordinator, $director]);

        for ($i = 1; $i <= 20; $i++) {
            $author = $authors->random();
            $isPublished = fake()->boolean(90); // 90% będzie opublikowanych

            $post = Post::create([
                'title' => fake()->sentence(rand(3, 8)),
                'content' => fake()->paragraphs(rand(2, 5), true),
                'image' => null,
                'is_published' => $isPublished,
                'published_at' => $isPublished ? now()->subDays(rand(0, 30)) : null,
                'author_id' => $author->id,
            ]);
            $posts[] = $post;
        }

        // 60 losowych reakcji na posty
        for ($i = 1; $i <= 60; $i++) {
            $post = fake()->randomElement($posts);
            $user = $allUsers->random();

            // Sprawdź czy użytkownik już zareagował na ten post
            $exists = PostReaction::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                PostReaction::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'type' => fake()->randomElement(['like', 'dislike']),
                ]);
            }
        }

        // 50 losowych komentarzy do postów
        for ($i = 1; $i <= 50; $i++) {
            $post = fake()->randomElement($posts);
            $user = $allUsers->random();

            Comment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'content' => fake()->sentences(rand(1, 3), true),
            ]);
        }

        $this->command->info('✅ Utworzono:');
        $this->command->info('   - 1 administratora');
        $this->command->info('   - 1 koordynatora');
        $this->command->info('   - 1 dyrektora');
        $this->command->info('   - 3 instruktorów (w tym wychowawcy)');
        $this->command->info('   - 4 grupy uczniowskie: 4OPW1 (14), 4OPW2 (12), 5CM (3), 3OPW1 (1)');
        $this->command->info('   - 30 studentów łącznie w grupach');
        $this->command->info('   - 20 postów');
        $this->command->info('   - 60 reakcji na posty');
        $this->command->info('   - 50 komentarzy');

        // Dodajmy przykładowy sprzęt aby skaner miał co wyszukiwać
        $this->seedEquipments();
    }

    protected function seedEquipments(): void
    {
        $miniCodes = ['ARES', 'MARS', 'ZEUS', 'HERCULES', 'ACHILLES', 'PERSEUS', 'AJAX'];
        $avataCodes = ['ODIN', 'THOR', 'TYR', 'JUPITER', 'HADES', 'KRONOS', 'VULCAN'];

        $equipments = [];

        foreach ($miniCodes as $code) {
            $equipments[] = ['name' => "Dron DJI Mini 5 Pro - {$code}", 'model' => "DJI-M5P-{$code}", 'category' => 'drone', 'status' => 'available', 'description' => "Dron DJI Mini 5 Pro Fly More Combo (RC2) - {$code}"];
            $equipments[] = ['name' => "Inteligentny akumulator Plus BWXNN5-4680-7.16 - {$code}", 'model' => "BWXNN5-4680-{$code}", 'category' => 'battery', 'status' => 'available', 'description' => "Akumulator Plus do DJI Mini 5 Pro - {$code}"];
            $equipments[] = ['name' => "Inteligentny akumulator BWXNN5-2788-7.0 - {$code}", 'model' => "BWXNN5-2788-{$code}", 'category' => 'battery', 'status' => 'available', 'description' => "Akumulator standardowy do DJI Mini 5 Pro - {$code}"];
            $equipments[] = ['name' => "Zestaw filtrów ND (ND 8/32/128) - {$code}", 'model' => "FILTER-ND-{$code}", 'category' => 'parts', 'status' => 'available', 'description' => "Filtry ND do DJI Mini 5 Pro - {$code}"];
            $equipments[] = ['name' => "Torba transportowa DJI Mini 5 Pro - {$code}", 'model' => "BAG-M5P-{$code}", 'category' => 'bag', 'status' => 'available', 'description' => "Torba do przechowywania zestawu - {$code}"];
            $equipments[] = ['name' => "Kontroler RC2 - {$code}", 'model' => "RC2-{$code}", 'category' => 'controller', 'status' => 'available', 'description' => "Kontroler RC2 do DJI Mini 5 Pro - {$code}"];
            $equipments[] = ['name' => "Zestaw śmigieł - {$code}", 'model' => "PROPS-M5P-{$code}", 'category' => 'parts', 'status' => 'available', 'description' => "Zapasowe śmigła do DJI Mini 5 Pro - {$code}"];
        }

        foreach ($avataCodes as $code) {
            $equipments[] = ['name' => "Dron DJI Avata 2 - {$code}", 'model' => "DJI-AVT2-{$code}", 'category' => 'drone', 'status' => 'available', 'description' => "Dron DJI Avata 2 Fly More Combo - {$code}"];
            $equipments[] = ['name' => "Inteligentny akumulator Avata 2570mAh - {$code} 1", 'model' => "AVT2-BAT-{$code}-1", 'category' => 'battery', 'status' => 'available', 'description' => "Akumulator do DJI Avata 2 - {$code}"];
            $equipments[] = ['name' => "Inteligentny akumulator Avata 2570mAh - {$code} 2", 'model' => "AVT2-BAT-{$code}-2", 'category' => 'battery', 'status' => 'available', 'description' => "Akumulator do DJI Avata 2 - {$code}"];
            $equipments[] = ['name' => "Inteligentny akumulator Avata 2570mAh - {$code} 3", 'model' => "AVT2-BAT-{$code}-3", 'category' => 'battery', 'status' => 'available', 'description' => "Akumulator do DJI Avata 2 - {$code}"];
            $equipments[] = ['name' => "Gogle FPV DJI Avata 2 - {$code}", 'model' => "FPV-AVT2-{$code}", 'category' => 'fpv', 'status' => 'available', 'description' => "Gogle FPV do DJI Avata 2 - {$code}"];
            $equipments[] = ['name' => "Torba transportowa DJI Avata 2 - {$code}", 'model' => "BAG-AVT2-{$code}", 'category' => 'bag', 'status' => 'available', 'description' => "Torba do przechowywania zestawu - {$code}"];
            $equipments[] = ['name' => "Zestaw śmigieł DJI Avata 2 - {$code}", 'model' => "PROPS-AVT2-{$code}", 'category' => 'parts', 'status' => 'available', 'description' => "Zapasowe śmigła do DJI Avata 2 - {$code}"];
        }

        $createdEquipment = [];
        foreach ($equipments as $index => $data) {
            $data['barcode'] = 'E-TEMP-' . $index;
            $eq = Equipment::create($data);
            $eq->update(['barcode' => BarcodeResolver::generateEquipmentBarcode($eq->id)]);
            $createdEquipment[] = $eq;
        }

        $this->command->info('   - ' . count($equipments) . ' sprzętów z kodami EXXXXXXXXXX');

        $this->seedEquipmentSets($createdEquipment, $miniCodes, $avataCodes);
    }

    protected function seedEquipmentSets(array $equipments, array $miniCodes, array $avataCodes): void
    {
        $miniSets = [];
        foreach ($miniCodes as $i => $code) {
            $offset = $i * 7;
            $miniSets[] = [
                'name' => "Dron DJI Mini 5 Pro Fly More Combo (RC2) - {$code}",
                'indices' => [
                    $offset + 0, // dron
                    $offset + 5, // kontroler
                    $offset + 1, // bateria plus
                    $offset + 2, // bateria standard
                    $offset + 3, // filtry
                    $offset + 4, // torba
                    $offset + 6, // smigla
                ],
            ];
        }

        foreach ($miniSets as $setData) {
            $set = \App\Models\EquipmentSet::create([
                'barcode' => 'Z-TEMP-' . uniqid(),
                'name' => $setData['name'],
                'description' => 'Kompletny zestaw DJI Mini 5 Pro Fly More Combo (RC2) zawierający drona, kontroler RC2, dwa akumulatory, filtry, torbę i zapasowe śmigła',
                'active' => true,
            ]);
            $set->update(['barcode' => BarcodeResolver::generateSetBarcode($set->id)]);

            $equipmentIds = [];
            foreach ($setData['indices'] as $idx) {
                if (isset($equipments[$idx])) {
                    $equipmentIds[] = $equipments[$idx]->id;
                }
            }
            $set->equipments()->attach($equipmentIds);
        }

        $avataSets = [];
        $base = count($miniCodes) * 7;
        foreach ($avataCodes as $i => $code) {
            $offset = $base + ($i * 7);
            $avataSets[] = [
                'name' => "Dron DJI Avata 2 Fly More Combo - {$code}",
                'indices' => [
                    $offset + 0, // dron
                    $offset + 1, // bateria 1
                    $offset + 2, // bateria 2
                    $offset + 3, // bateria 3
                    $offset + 4, // gogle FPV
                    $offset + 5, // torba
                    $offset + 6, // smigla
                ],
            ];
        }

        foreach ($avataSets as $setData) {
            $set = \App\Models\EquipmentSet::create([
                'barcode' => 'Z-TEMP-' . uniqid(),
                'name' => $setData['name'],
                'description' => 'Kompletny zestaw DJI Avata 2 Fly More Combo zawierający drona, gogle FPV, trzy akumulatory, torbę i zapasowe śmigła',
                'active' => true,
            ]);
            $set->update(['barcode' => BarcodeResolver::generateSetBarcode($set->id)]);

            $equipmentIds = [];
            foreach ($setData['indices'] as $idx) {
                if (isset($equipments[$idx])) {
                    $equipmentIds[] = $equipments[$idx]->id;
                }
            }
            $set->equipments()->attach($equipmentIds);
        }

        $this->command->info('   - 7 zestawów DJI Mini 5 Pro Fly More Combo (RC2): ' . implode(', ', $miniCodes));
        $this->command->info('   - 7 zestawów DJI Avata 2 Fly More Combo: ' . implode(', ', $avataCodes));
        $this->command->info('   - Łącznie 14 zestawów z kodami ZXXXXXXXXXX');
    }

    /**
     * Tworzy wskazaną liczbę studentów w danej grupie.
     */
    protected function seedStudentsForGroup(Group $group, int $count)
    {
        $students = collect();
        $offset = User::count();

        for ($i = 1; $i <= $count; $i++) {
            $student = User::create([
                'name' => fake()->firstName() . ' ' . fake()->lastName(),
                'email' => 'student' . ($offset + $i) . '@example.com',
                'password' => Hash::make('Haslo1234'),
                'pilot_license' => 'PL-STD-' . str_pad($offset + $i, 3, '0', STR_PAD_LEFT),
                'operator_license' => 'OP-STD-' . str_pad($offset + $i, 3, '0', STR_PAD_LEFT),
                'license_expiry_date' => now()->addYear(),
                'active' => true,
                'group_id' => $group->id,
            ]);
            $student->assignRole('student');
            $student->update(['barcode' => BarcodeResolver::generateStudentBarcode($student->id)]);
            $students->push($student);
        }

        return $students;
    }
}
