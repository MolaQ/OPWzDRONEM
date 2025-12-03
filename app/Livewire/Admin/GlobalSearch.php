<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\BarcodeResolver;
use App\Models\User;
use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.sidebar')]
class GlobalSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $isSearching = false;

    // Modal for details
    public bool $showModal = false;
    public ?array $selectedItem = null;
    public string $selectedType = '';

    protected BarcodeResolver $resolver;

    public function boot(BarcodeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function updatedQuery()
    {
        $this->search();
    }

    public function search()
    {
        $this->results = [];
        $q = trim($this->query);

        if (strlen($q) < 2) {
            return;
        }

        $this->isSearching = true;

        // Try to resolve as barcode first
        $barcodeResult = null;
        if ($this->resolver->isValidFormat(strtoupper($q))) {
            $barcodeResult = $this->resolver->resolve(strtoupper($q));
        }

        // Search Users (Students)
        $users = User::where(function($query) use ($q) {
            $query->where('name', 'LIKE', "%{$q}%")
                ->orWhere('email', 'LIKE', "%{$q}%")
                ->orWhere('barcode', 'LIKE', "%{$q}%");
        })
        ->limit(10)
        ->get()
        ->map(function($user) {
            // Get active rentals for this user
            $activeRentals = Rental::whereHas('rentalGroup.users', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->whereNull('returned_at')
            ->with(['equipment', 'equipmentSet'])
            ->get()
            ->map(function($rental) {
                $item = $rental->equipment ?? $rental->equipmentSet;
                return [
                    'type' => $rental->equipment ? 'Sprzęt' : 'Zestaw',
                    'name' => $item?->name,
                    'barcode' => $item?->barcode,
                    'rented_at' => $rental->rented_at->format('d.m.Y H:i'),
                ];
            })->toArray();

            return [
                'id' => $user->id,
                'type' => 'student',
                'name' => $user->name,
                'email' => $user->email,
                'barcode' => $user->barcode,
                'group' => $user->group?->name,
                'active_rentals' => $activeRentals,
                'rentals_count' => count($activeRentals),
            ];
        })->toArray();

        // Search Equipment
        $equipment = Equipment::where(function($query) use ($q) {
            $query->where('name', 'LIKE', "%{$q}%")
                ->orWhere('barcode', 'LIKE', "%{$q}%")
                ->orWhere('description', 'LIKE', "%{$q}%");
        })
        ->limit(10)
        ->get()
        ->map(function($item) {
            // Check if currently rented
            $activeRental = Rental::where('equipment_id', $item->id)
                ->whereNull('returned_at')
                ->with(['rentalGroup.users'])
                ->first();

            $status = 'Dostępny';
            $statusColor = 'green';
            $rentedBy = null;

            if ($activeRental) {
                $status = 'Wypożyczony';
                $statusColor = 'orange';
                $rentedBy = [
                    'users' => $activeRental->rentalGroup->users->pluck('name')->toArray(),
                    'rented_at' => $activeRental->rented_at->format('d.m.Y H:i'),
                ];
            } elseif ($item->status !== 'dostepny') {
                // Show actual equipment status from database
                $statusMap = [
                    'dostepny' => ['label' => 'Dostępny', 'color' => 'green'],
                    'wypozyczony' => ['label' => 'Wypożyczony', 'color' => 'orange'],
                    'w_uzyciu' => ['label' => 'W użyciu', 'color' => 'blue'],
                    'konserwacja' => ['label' => 'Konserwacja', 'color' => 'yellow'],
                    'uszkodzony' => ['label' => 'Uszkodzony', 'color' => 'red'],
                ];

                $statusInfo = $statusMap[$item->status] ?? ['label' => $item->status, 'color' => 'gray'];
                $status = $statusInfo['label'];
                $statusColor = $statusInfo['color'];
            }

            return [
                'id' => $item->id,
                'type' => 'equipment',
                'name' => $item->name,
                'barcode' => $item->barcode,
                'description' => $item->description,
                'status' => $status,
                'status_color' => $statusColor,
                'rented_by' => $rentedBy,
            ];
        })->toArray();

        // Search Equipment Sets
        $equipmentSets = EquipmentSet::where(function($query) use ($q) {
            $query->where('name', 'LIKE', "%{$q}%")
                ->orWhere('barcode', 'LIKE', "%{$q}%")
                ->orWhere('description', 'LIKE', "%{$q}%");
        })
        ->withCount('equipments')
        ->limit(10)
        ->get()
        ->map(function($set) {
            // Check if currently rented
            $activeRental = Rental::where('equipment_set_id', $set->id)
                ->whereNull('returned_at')
                ->with(['rentalGroup.users'])
                ->first();

            $status = 'Dostępny';
            $statusColor = 'green';
            $rentedBy = null;

            if ($activeRental) {
                $status = 'Wypożyczony';
                $statusColor = 'orange';
                $rentedBy = [
                    'users' => $activeRental->rentalGroup->users->pluck('name')->toArray(),
                    'rented_at' => $activeRental->rented_at->format('d.m.Y H:i'),
                ];
            } elseif (!$set->active) {
                $status = 'Nieaktywny';
                $statusColor = 'gray';
            }

            return [
                'id' => $set->id,
                'type' => 'equipment_set',
                'name' => $set->name,
                'barcode' => $set->barcode,
                'description' => $set->description,
                'equipments_count' => $set->equipments_count,
                'status' => $status,
                'status_color' => $statusColor,
                'rented_by' => $rentedBy,
            ];
        })->toArray();

        // Search Posts
        $posts = Post::where(function($query) use ($q) {
            $query->where('title', 'LIKE', "%{$q}%")
                ->orWhere('content', 'LIKE', "%{$q}%");
        })
        ->where('is_published', true)
        ->with('author')
        ->limit(5)
        ->get()
        ->map(function($post) {
            return [
                'id' => $post->id,
                'type' => 'post',
                'title' => $post->title,
                'excerpt' => \Illuminate\Support\Str::limit(strip_tags($post->content), 150),
                'author' => $post->author?->name,
                'published_at' => $post->published_at?->format('d.m.Y'),
                'views' => 0,
            ];
        })->toArray();

        // Search Comments
        $comments = Comment::where(function($query) use ($q) {
            $query->where('content', 'LIKE', "%{$q}%");
        })
        ->with(['user', 'post'])
        ->limit(5)
        ->get()
        ->map(function($comment) {
            return [
                'id' => $comment->id,
                'type' => 'comment',
                'content' => \Illuminate\Support\Str::limit($comment->content, 150),
                'author' => $comment->user?->name,
                'post_title' => $comment->post?->title,
                'post_id' => $comment->post_id,
                'created_at' => $comment->created_at->format('d.m.Y H:i'),
            ];
        })->toArray();

        // Group results
        $this->results = [
            'students' => $users,
            'equipment' => $equipment,
            'equipment_sets' => $equipmentSets,
            'posts' => $posts,
            'comments' => $comments,
            'barcode_match' => $barcodeResult,
        ];

        $this->isSearching = false;
    }

    public function clearSearch()
    {
        $this->reset(['query', 'results']);
    }

    public function showDetails($type, $id)
    {
        $this->selectedType = $type;

        // Load full details based on type
        switch ($type) {
            case 'student':
                $user = User::with(['group'])->find($id);

                if ($user) {
                    // Load student's active rentals without ambiguous joins
                    $activeRentals = Rental::whereHas('rentalGroup.users', function($query) use ($user) {
                            $query->where('users.id', $user->id);
                        })
                        ->whereNull('returned_at')
                        ->with(['equipment', 'equipmentSet'])
                        ->get()
                        ->map(function($rental) {
                            $item = $rental->equipment ?? $rental->equipmentSet;
                            return [
                                'type' => $rental->equipment ? 'Sprzęt' : 'Zestaw',
                                'name' => $item?->name,
                                'barcode' => $item?->barcode,
                                'rented_at' => $rental->rented_at->format('d.m.Y H:i'),
                            ];
                        })->toArray();

                    $this->selectedItem = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'barcode' => $user->barcode,
                        'group' => $user->group?->name,
                        'created_at' => $user->created_at->format('d.m.Y H:i'),
                        'active_rentals' => $activeRentals,
                        'rentals_count' => count($activeRentals),
                    ];
                }
                break;

            case 'equipment':
                $equipment = Equipment::with(['rentals' => function($q) {
                    $q->whereNull('returned_at')->with('rentalGroup.users');
                }])->find($id);

                if ($equipment) {
                    $activeRental = $equipment->rentals->first();
                    $this->selectedItem = [
                        'id' => $equipment->id,
                        'name' => $equipment->name,
                        'barcode' => $equipment->barcode,
                        'model' => $equipment->model,
                        'category' => $equipment->category,
                        'status' => $equipment->status,
                        'description' => $equipment->description,
                        'active_rental' => $activeRental ? [
                            'users' => $activeRental->rentalGroup->users->pluck('name')->toArray(),
                            'rented_at' => $activeRental->rented_at->format('d.m.Y H:i'),
                        ] : null,
                    ];
                }
                break;

            case 'equipment_set':
                $set = EquipmentSet::with(['equipments', 'rentals' => function($q) {
                    $q->whereNull('returned_at')->with('rentalGroup.users');
                }])->find($id);

                if ($set) {
                    $activeRental = $set->rentals->first();
                    $this->selectedItem = [
                        'id' => $set->id,
                        'name' => $set->name,
                        'barcode' => $set->barcode,
                        'description' => $set->description,
                        'active' => $set->active,
                        'equipments' => $set->equipments->map(function($eq) {
                            return [
                                'name' => $eq->name,
                                'barcode' => $eq->barcode,
                                'status' => $eq->status,
                            ];
                        })->toArray(),
                        'active_rental' => $activeRental ? [
                            'users' => $activeRental->rentalGroup->users->pluck('name')->toArray(),
                            'rented_at' => $activeRental->rented_at->format('d.m.Y H:i'),
                        ] : null,
                    ];
                }
                break;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedItem = null;
        $this->selectedType = '';
    }

    public function render()
    {
        return view('livewire.admin.global-search');
    }
}
