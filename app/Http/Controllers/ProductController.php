public function index()
{
    // Data Dummy (Pura-pura ambil dari database)
    // Nanti kalau sudah connect DB beneran, kita ganti jadi: $products = Product::all();
    $products = [
        [
            'id' => 1,
            'image' => 'https://via.placeholder.com/50',
            'name' => 'Sepatu Futsal Nike',
            'category' => 'Olahraga',
            'price' => 500000,
            'rating' => 4.8,
            'reviews' => 20,
            'stock' => 12,
        ],
        [
            'id' => 2,
            'image' => 'https://via.placeholder.com/50',
            'name' => 'Kemeja Flannel',
            'category' => 'Pakaian',
            'price' => 150000,
            'rating' => 3.0,
            'reviews' => 5,
            'stock' => 5,
        ],
    ];

    // Mengirim data $products ke file View
    return view('products.index', compact('products'));
}
