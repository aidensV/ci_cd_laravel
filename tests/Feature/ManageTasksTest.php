<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Task;

class ManageTasksTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function user_can_create_a_task()
    {
        // User memngunjungi halaman Daftar Task
        $this->visit('/tasks');

        // Isi form `name` dan `description` kemudian submit
        $this->submitForm('Create Task', [
          'name' => 'My First Task',
          'description' => 'This is my first task on my new job.',
      ]);

        // Lihat Record tersimpan ke database
        $this->seeInDatabase('tasks', [
          'name' => 'My First Task',
          'description' => 'This is my first task on my new job.',
          'is_done' => 0,
      ]);

        // Redirect ke halaman Daftar Task
        $this->seePageIs('/tasks');

        // Tampil hasil task yang telah diinput
        $this->see('My First Task');
        $this->see('This is my first task on my new job.');
    }
  /** @test */
  public function task_entry_must_pass_validation()
  {
      // Submit form untuk membuat task baru
      // dengan field name description kosong
      $this->post('/taskscreate', [
          'name'        => '',
          'description' => '',
      ]);

      // Cek pada session apakah ada error untuk field nama dan description
      $this->assertSessionHasErrors(['name', 'description']);
  }

    /** @test */
    public function user_can_browser_tasks_index_page()
    {
        // Generate 3 record task pada table `tasks`.
        $tasks = factory(Task::class, 3)->create();

        // User membuka halaman Daftar Task.
        $this->visit('/tasks');

        // User melihat ketiga task tampil pada halaman.
        $this->see($tasks[0]->name);
        $this->see($tasks[1]->name);
        $this->see($tasks[2]->name);

        // User melihat link untuk edit task pada masing-masing item task.
        
        // <a href="/tasks?action=edit&id=1" id="edit_task_1">edit</a>
        $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[0]->id,
            'href' => url('tasks?action=edit&id='.$tasks[0]->id)
        ]);

        // <a href="/tasks?action=edit&id=2" id="edit_task_2">edit</a>
        $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[1]->id,
            'href' => url('tasks?action=edit&id='.$tasks[1]->id)
        ]);

        // <a href="/tasks?action=edit&id=3" id="edit_task_3">edit</a>
        $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[2]->id,
            'href' => url('tasks?action=edit&id='.$tasks[2]->id)
        ]);
    }

    /** @test */
    public function user_can_edit_an_existing_task()
    {
      // Generate 1 Record task pada table 'tasks'
      $task = factory(Task::class)->create();
      // User membuka halaman daftar Task.
      $this->visit('/tasks');
      //  klik tombol edit task
      $this->click('edit_task_'.$task->id);
      // Lihat URL yang dituju sesuai dengan target
      $this->seePageIs('/tasks?action=edit&id='.$task->id);
      // Tampil form Edit Task
      $this->seeElement('form',[
          'id'      =>  'edit_task_'.$task->id,
          'action'  => url('tasks/'.$task->id),
      ]);
      // User submit form berisi nama dan deskripsi
      $this->submitForm('Update Task', [
          'name' => 'Updated Task',
          'description' => 'Updated task description.',
      ]);
      // Lihat halaman web ter-redirect ke url sesuai dengan target
      $this->seePageIs('/tasks');
      // Record pada database berubah susai dengan dan deskripsi
      $this->seeInDatabase('tasks',[
          'id' => $task->id,
          'name' => 'Updated Task',
          'description' => 'Updated task description.',
      ]);
    }

    /** @test */
    public function user_can_delete_an_existing_task()
    {
        // Generate 1 record task pada table 'task'
        $task = factory(Task::class)->create();
        // user membuka halaman daftar task
        $this->visit('/tasks');
        // User tekan tombol "Hapus Task"
        $this->press('delete_task_'.$task->id);
        // Lihat Halaman web ter-redirect ke halaman daftar task
        $this->seePageIs('/tasks');
        // record task hilang dari database
        $this->dontSeeInDatabase('tasks',[
            'id' => $task->id,
        ]);
    }
}
