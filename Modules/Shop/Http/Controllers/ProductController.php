<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\shop\Repositories\front\interfaces\ProductRepositoryInterfaces;
use Modules\shop\Repositories\front\interfaces\CategoryRepositoryInterfaces;
use Modules\shop\Repositories\front\interfaces\TagRepositoryInterfaces;


// import model productnya
use Modules\Shop\Entities\Product;

class ProductController extends Controller
{

    protected $productRepository;
    protected $categoryRepository;
    protected $tagRepository;
    protected $defaultPriceRange;
    protected $sortingQuery;

        // Konstruktor dengan dependency injection
    public function __construct(ProductRepositoryInterfaces $productRepository, 
                                CategoryRepositoryInterfaces $categoryRepository,
                                TagRepositoryInterfaces $tagRepository)

    {

        // Menyimpan alat (repository) ke dalam properti kelas
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->defaultPriceRange = [
            'min' => 10000,
            'max' => 75000,
        ];

        $this->data['categories'] = $this->categoryRepository->findAll();
        // dd($this->data);
        
        $this->data['filter']['price'] = $this->defaultPriceRange;

        $this->sortingQuery = null;
        $this->data['sortingQuery'] = $this->sortingQuery;
        $this->data['sortingOptions'] = [
            '' => '-- Sort Products --',
            '?sort=price&order=asc' => 'Price: Low to High',
            '?sort=price&order=desc' => 'Price: High to Low',
            '?sort=publish_date&order=desc' => 'Newest Item',
        ];
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
        // Metode yang menggunakan alat tersebut

    public function create()
    {
        // return view('products.create');
        return $this->loadTheme('products.create', $this->data);

    }


    public function store(Request $request)
    
        {
            $data = $request->all();
            $this->productRepository->create($data);
            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        }
    

    public function edit($id)
    {
        $product = $this->productRepository->findById($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->productRepository->update($id, $data);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $this->productRepository->delete($id);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }



    public function index(Request $request)
    {
        // return view('shop::index');

        // $products = Product::get();
        // dd($products);

        // $this-> data['products'] = Product::paginate($this->perPage);
        $priceFilter =$this->getPriceRangeFilter($request);
        $options=[
            'per_page' => $this->perPage,
            'filter'=> [
                'price' => $priceFilter,
            ]
        ];

        if ($request->get('price')){
            $this-> data['filter']['price']=$priceFilter;
        }


    
        if ($request->get('sort')) {
            $sort = $this->sortingRequest($request);
            $options['sort'] = $sort;

            $this->sortingQuery = '?sort=' . $sort['sort'] . '&order=' . $sort['order'];
            
            $this->data['sortingQuery'] = $this->sortingQuery;
        }
        
        
        $this-> data['products'] =$this->productRepository->findAll($options);



        return $this->loadTheme('products.index1', $this->data);


    }

    public function category($categorySlug)
    {
        $category = $this->categoryRepository->findBySlug($categorySlug);

        $options = [
            'per_page' => $this->perPage,
            'filter' => [
                'category' => $categorySlug,
            ]
        ];

        $this->data['products'] = $this->productRepository->findAll($options);
        $this->data['category'] = $category;

        return $this->loadTheme('products.category', $this->data);
    }

    public function tag($tagSlug)
    {
        // dd('tes');
        $tag=$this->tagRepository->findBySlug($tagSlug);
        // dd($tag);
        
        $options = [
            'per_page' => $this->perPage,
            'filter' => [
                'tag' => $tagSlug,
            ]
        ];

        $this->data['products'] = $this->productRepository->findAll($options);
        $this->data['tag'] = $tag;


        return $this->loadTheme('products.tag', $this->data);

    } 
    
    public function show($categorySlug, $productSlug){
        // dd($productSlug);
        $sku=Arr::last(explode('-', $productSlug));
        // dd($sku);
           
            $product = $this->productRepository->findBySKU($sku);
    
            $this->data['product'] = $product;
    
            return $this->loadTheme('products.show', $this->data);

    }

    
    function getPriceRangeFilter($request)
    {
        if (!$request->get('price')) {
            return [];
        }

        $prices = explode(' - ', $request->get('price'));
        if (count($prices) < 0) {
            return $this->defaultPriceRange;
        }

        return [
            'min' => (int) $prices[0],
            'max' => (int) $prices[1],
        ];
    }

        function sortingRequest(Request $request) {
            $sort = [];

            if ($request->get('sort') && $request->get('order')) {
                $sort = [
                    'sort' => $request->get('sort'),
                    'order' => $request->get('order'),
                ];
            } else if ($request->get('sort')) {
                $sort = [
                    'sort' => $request->get('sort'),
                    'order' => 'desc',
                ];
            }

            return $sort;
        }
    

    /**
     * Show the form for creating a new resource.
     * @return Renderable
      */
    // public function create()
    // {
    //     return view('shop::create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  * @param Request $request
    //  * @return Renderable
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Show the specified resource.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function show($id)
    // {
    //     return view('shop::show');
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function edit($id)
    // {
    //     return view('shop::edit');
    // }

    // /**
    //  * Update the specified resource in storage.
    //  * @param Request $request
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
