<?php

namespace App\Http\Livewire;

use App\Helpers\GlobalApp;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $barcode, $cost, $price, $stock, $alerts, $category_id, $image, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;
    protected $listeners = ['deleteRow' => 'Destroy'];

    protected function getRulesAndMessages($type = null)
    {
        $rules = [
            'name' => 'required|min:3|unique:products',
            'category_id' => 'required',
            'barcode' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
        ];
        $type === 'update' && $rules['name'] .= ",name,{$this->selected_id}";
        $messages = [
            'name.required' => 'Nombre del producto es requerido',
            'name.unique' => 'Nombre del producto ya existe',
            'name.min' => 'Nombre del producto debe tener al menos 3 caracteres',
            'category_id.required' => 'La categoria es requerida',
            'barcode.required' => 'El codigo es requerida',
            'price.required' => 'El precio es requerida',
        ];
        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    public function mount()
    {
        $this->pageTitle = "Listado";
        $this->componentName = "Productos";
        $this->category_id = null;
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        $products = Product::when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('barcode', 'like', '%' . $this->search . '%')
                ->orWhere(function ($q2) {
                    $q2->whereHas('category', function ($q3) {
                        $q3->where('name', 'like', '%' . $this->search . '%');
                    });
                });
        })->orderBy('name', 'asc')
            ->paginate($this->pagination);

        $categories =  Category::orderBy('name')->get();
        $image = "";

        return view('livewire.product.products', compact('products', 'categories', 'image'))
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Store()
    {
        $rules = $this->getRulesAndMessages('store');
        $this->validate($rules['rules'], $rules['messages']);

        $data['name'] = $this->name;
        $data['barcode'] = $this->barcode;
        $data['cost'] = str_replace(",", "", $this->cost);
        $data['price'] = str_replace(",", "", $this->price);
        $data['stock'] = str_replace(",", "", $this->stock);
        $data['alerts'] = $this->alerts;
        $data['category_id'] = $this->category_id;

        if ($this->image) {
            $customFileName = GlobalApp::saveFile($this->image, 'products');
            $data['image'] =  $customFileName;
        }

        Product::create($data);

        $this->resetUI();
        $this->emit('product-added', 'Producto Registrado');
    }

    public function resetUI()
    {
        $this->name = '';
        $this->barcode = '';
        $this->cost = '';
        $this->stock = '';
        $this->price = '';
        $this->alerts = '';
        $this->category_id = '';
        $this->image = null;
        $this->search = '';
        $this->selected_id = 0;
    }
}
