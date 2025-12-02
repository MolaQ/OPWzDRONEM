<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Comment;
use App\Services\BarcodeResolver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzymy grupy
        $groupAdmin = Group::create([
            'name' => 'Administracja',
            'description' => 'Grupa administratorów systemu',
            'active' => true,
        ]);

        $group4OPW = Group::create([
            'name' => '4OPW',
            'description' => 'Klasa 4OPW 2025/2026',
            'active' => true,
        ]);

        // 1 Administrator
        $admin = User::create([
            'name' => 'Administrator Systemu',
            'email' => 'admin@example.com',
            'password' => Hash::make('Haslo1234'),
            'role' => 'admin',
            'pilot_license' => 'PL-ADMIN-001',
            'operator_license' => 'OP-ADMIN-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => $groupAdmin->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['barcode' => BarcodeResolver::generateStudentBarcode($admin->id)]);

        // 2 Instruktorzy
        $instructor1 = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan.kowalski@example.com',
            'password' => Hash::make('Haslo1234'),
            'role' => 'instructor',
            'pilot_license' => 'PL-INST-001',
            'operator_license' => 'OP-INST-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => $groupAdmin->id,
        ]);
        $instructor1->assignRole('instructor');
        $instructor1->update(['barcode' => BarcodeResolver::generateStudentBarcode($instructor1->id)]);

        $instructor2 = User::create([
            'name' => 'Anna Nowak',
            'email' => 'anna.nowak@example.com',
            'password' => Hash::make('Haslo1234'),
            'role' => 'instructor',
            'pilot_license' => 'PL-INST-002',
            'operator_license' => 'OP-INST-002',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => $groupAdmin->id,
        ]);
        $instructor2->assignRole('instructor');
        $instructor2->update(['barcode' => BarcodeResolver::generateStudentBarcode($instructor2->id)]);

        // 1 Wychowawca
        $wychowawca = User::create([
            'name' => 'Piotr Wiśniewski',
            'email' => 'piotr.wisniewski@example.com',
            'password' => Hash::make('Haslo1234'),
            'role' => 'instructor',
            'pilot_license' => 'PL-WYCH-001',
            'operator_license' => 'OP-WYCH-001',
            'license_expiry_date' => now()->addYears(2),
            'active' => true,
            'group_id' => $group4OPW->id,
        ]);
        $wychowawca->assignRole('instructor');
        $wychowawca->update(['barcode' => BarcodeResolver::generateStudentBarcode($wychowawca->id)]);

        // 30 użytkowników (studentów) w grupie 4OPW
        $students = [];
        for ($i = 1; $i <= 30; $i++) {
            $student = User::create([
                'name' => fake()->firstName() . ' ' . fake()->lastName(),
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('Haslo1234'),
                'role' => 'student',
                'pilot_license' => 'PL-STD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'operator_license' => 'OP-STD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'license_expiry_date' => now()->addYear(),
                'active' => true,
                'group_id' => $group4OPW->id,
            ]);
            $student->assignRole('student');
            $student->update(['barcode' => BarcodeResolver::generateStudentBarcode($student->id)]);
            $students[] = $student;
        }

        // Zbieramy wszystkich użytkowników do losowania
        $allUsers = collect([$admin, $instructor1, $instructor2, $wychowawca])->merge($students);

        // 20 postów
        $posts = [];
        $authors = collect([$admin, $instructor1, $instructor2, $wychowawca]);

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
        $this->command->info('   - 2 instruktorów');
        $this->command->info('   - 1 wychowawcę');
        $this->command->info('   - 30 studentów w grupie 4OPW');
        $this->command->info('   - 20 postów');
        $this->command->info('   - 60 reakcji na posty');
        $this->command->info('   - 50 komentarzy');
    }
}
