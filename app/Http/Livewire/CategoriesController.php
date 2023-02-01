<?php

namespace App\Http\Livewire;

use App\Helpers\GlobalApp;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CategoriesController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $image, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;
    protected $listeners = ['deleteRow' => 'Destroy'];

    protected function getRulesAndMessages($type = null)
    {
        $rules = [
            'name' => 'required|min:3|unique:categories',
        ];
        $type === 'update' && $rules['name'] .= ",name,{$this->selected_id}";
        $messages = [
            'name.required' => 'Nombre de la categoria es requerido',
            'name.unique' => 'Nombre de la categoria ya existe',
            'name.min' => 'Nombre de la categoria debe tener al menos 3 caracteres',
        ];
        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }


    public function mount()
    {
        $this->pageTitle = "Listado";
        $this->componentName = "Categorias";
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        $categories = Category::when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        })->orderBy('id', 'desc')->paginate($this->pagination);

        return view('livewire.category.categories', compact('categories'))
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Edit($id)
    {
        $record = Category::find($id, ['id', 'name', 'image']);
        $this->name = $record->name;
        $this->selected_id = $record->id;
        $this->image = null;

        $this->emit('show-modal', 'Modal para editar');
    }

    public function Store()
    {
        $rules = $this->getRulesAndMessages('store');
        $this->validate($rules['rules'], $rules['messages']);

        $data['name'] = $this->name;

        if ($this->image) {
            $customFileName = GlobalApp::saveFile($this->image, 'categories');
            $data['image'] =  $customFileName;
        }

        Category::create($data);

        $this->resetUI();
        $this->emit('category-added', 'Categoria Registrada');
    }

    public function Update()
    {
        $rules = $this->getRulesAndMessages('update');
        $this->validate($rules['rules'], $rules['messages']);

        $category = Category::find($this->selected_id);
        $category->update([
            'name' => $this->name
        ]);

        if ($this->image) {
            $customFileName = GlobalApp::saveFile($this->image, 'categories');
            $imageName = $category->image;

            $category->image = $customFileName;
            $category->save();

            if ($imageName != null && file_exists('storage/categories/' . $imageName)) {
                unlink('storage/categories/' . $imageName);
            }
        }

        $this->resetUI();
        $this->emit('category-updated', 'Categoría Actualizada');
    }

    public function resetUI()
    {
        $this->name = '';
        $this->image = null;
        $this->search = '';
        $this->selected_id = 0;
    }

    public function Destroy(Category $category)
    {
        $imageName = $category->image;
        $category->delete();

        if ($imageName) {
            unlink('storage/categories/' . $imageName);
        }

        $this->resetUI();
        $this->emit('category-deleted', 'Categoría Eliminada');
    }
}
