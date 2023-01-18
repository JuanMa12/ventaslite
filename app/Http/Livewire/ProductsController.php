<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $barcode, $cost, $price, $stock, $alerts, $categoryid, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;
    protected $listeners = ['deleteRow' => 'Destroy'];

    public function mount()
    {
        $this->pageTitle = "Listado";
        $this->componentName = "Productos";
        $this->categoryid = "Elegir";
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

        $categories =  [];
        $image = "";

        return view('livewire.product.products', compact('products', 'categories', 'image'))
            ->extends('layouts.theme.app')
            ->section('content');
    }
}
