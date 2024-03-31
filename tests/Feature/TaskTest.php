<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile; // Ditambahkan
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate; // Ditambahkan


class TaskTest extends TestCase
{
    use RefreshDatabase;
    // Mendefinisikan sebuah property class, $mockedUsers untuk menyimpan data User
    private $mockedUsers = [];
    private $mockedTasks = []; // Ditambahkan

    protected function setUp(): void
    {
        parent::setUp();
        // Membuat dan menyimpan 2(dua) data User menggunakan factory
        User::factory()->create();
        User::factory()->create();

        // Menentukan data User pertama ke $user1
        $user1 = User::first();

        // Menentukan data User lainnya ke $user2
        $user2 = User::where('id', '!=', $user1->id)->first();

        // Menambahkan data $user1 dan data $user2 ke mockedUsers
        array_push($this->mockedUsers, $user1, $user2);

        // Proses Autentikasi: Login dengan data $user1
        $this->actingAs($user1);

         // Tambahkan code di bawah ini
         $tasks = [
            [
                'name' => 'Task 1',
                'status' => Task::STATUS_NOT_STARTED,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 2',
                'status' => Task::STATUS_IN_PROGRESS,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 3',
                'status' => Task::STATUS_COMPLETED,
                'user_id' => $user1->id,
            ],
            [
                'name' => 'Task 4',
                'status' => Task::STATUS_COMPLETED,
                'user_id' => $user2->id,
            ],
        ];

        Task::insert($tasks);

        $this->mockedTasks = Task::with('user', 'files')
        ->get()
        ->toArray();

    }

    public function test_redirect_not_logged_in_user(): void
    {
        Auth::logout();

        $response = $this->get(route('home'));
        $response->assertStatus(302);
    }

    public function test_home(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Tambahkan code di bawah
        $response->assertViewIs('home');
        $response->assertViewHas('completed_count');
        $response->assertViewHas('uncompleted_count');

        $completed_count = $response->viewData('completed_count');
        $uncompleted_count = $response->viewData('uncompleted_count');

        $this->assertEquals(1, $completed_count);
        $this->assertEquals(2, $uncompleted_count);
    }

    public function test_index_with_right_permission(): void
    {
        Gate::shouldReceive('allows')
            ->with('viewAnyTask', Task::class)
            ->andReturn(true);
        Gate::shouldReceive('any')->andReturn(false);
        Gate::shouldReceive('check')->andReturn(false);

        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);

        $tasks = $response->viewData('tasks');

        // Data task dari semua pengguna
        $expectedTasks = $this->mockedTasks;

        $this->assertEquals($expectedTasks, $tasks->toArray());
    }

    public function test_create()
    {
        $response = $this->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
        $response->assertViewHas('pageTitle');

        $pageTitle = $response->viewData('pageTitle');

        $this->assertEquals('Create Task', $pageTitle);
    }

    public function test_store_with_file()
    {
        // Mempersiapkan penyimpanan file dalam disk "public"
        Storage::fake('public');

        $newTask = [
            'name' => 'New Task',
            'detail' => 'New Task detail',
            'due_date' => date('Y-m-d', time()),
            'status' => Task::STATUS_IN_PROGRESS,
        ];

        // Melakukan file upload ke tempat penyimpanan tersebut
        $file = UploadedFile::fake()->image('test_image.png');

        // Menyimpan data task beserta dengan file
        $response = $this->post(
            route('tasks.store'),
            array_merge($newTask, ['file' => $file])
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', $newTask);

        // Memeriksa apakah data task yang tersimpan memiliki file
        $task = Task::where('name', 'New Task')->first();
        $this->assertNotNull($task->files);

        // Memeriksa apakah file yang tersimpan dalam disc "public" dengan menggunakan file path
        $filePath = $task->files[0]->path;
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_store_invalid_request()
    {
        $response = $this->post(route('tasks.store'), [
            'detail' => 'New Task',
        ]);

        $response->assertSessionHasErrors(['name', 'due_date', 'status']);
    }

    public function test_edit()
    {
        $taskId = 1;
        $response = $this->get(route('tasks.edit', ['id' => $taskId]));
        $response->assertStatus(403);
    }

    public function test_update_with_file()
    {
        // Membuat pengguna untuk pengujian
        $user = factory(User::class)->create(); // Menggunakan create() untuk membuat pengguna baru

        // Autentikasi pengguna
        $this->actingAs($user);

        // Mempersiapkan penyimpanan file dalam disk "public"
        Storage::fake('public');
        $taskId = 1;
        $newTask = [
            'name' => 'New Task',
            'detail' => 'New Task detail',
            'due_date' => date('Y-m-d', time()),
            'status' => Task::STATUS_IN_PROGRESS,
        ];

        // Melakukan file upload ke tempat penyimpanan tersebut
        $file = UploadedFile::fake()->image('test_image.png');

        // Menyimpan data task beserta dengan file
        $response = $this->put(
            route('tasks.update', ['id' => $taskId]), // Gunakan metode PUT untuk update
            array_merge($newTask, ['file' => $file]) // Perhatikan key untuk file yang diunggah
        );

        $response->assertRedirect(route('tasks.index')); // Mengharapkan redirect ke tasks.index setelah update
        $response->assertSessionHasNoErrors();

        // Pastikan data task yang diperbarui disimpan dengan benar dalam database
        $this->assertDatabaseHas('tasks', $newTask);

        // Memeriksa apakah data task yang tersimpan memiliki file terlampir
        $task = Task::where('name', 'New Task')->first();
        $this->assertNotNull($task->file); // Pastikan relasi file terlampir tersedia

        // Memeriksa apakah file terlampir tersimpan dalam disk "public"
        Storage::disk('public')->assertExists($task->file->path); // Pastikan path file tersedia
    }




}
