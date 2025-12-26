<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk & Reputasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Font custom mirip Google Sans/Roboto */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="flex h-screen">
        <aside class="w-64 bg-gray-100 border-r border-gray-200 hidden md:block">
            <div class="p-6">
                <h1 class="text-xl font-bold text-gray-700">Seller Dashboard</h1>
            </div>
            <nav class="mt-4">
                <a href="#" class="block py-3 px-6 text-gray-600 hover:bg-gray-200">Dashboard</a>
                <a href="#" class="block py-3 px-6 text-blue-600 bg-blue-50 font-bold border-r-4 border-blue-600">Products</a>
                <a href="#" class="block py-3 px-6 text-gray-600 hover:bg-gray-200">Reviews & Reputation</a>
                <a href="#" class="block py-3 px-6 text-gray-600 hover:bg-gray-200">Orders</a>
                <a href="#" class="block py-3 px-6 text-gray-600 hover:bg-gray-200">Profile</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Manajemen Produk & Reputasi</h2>

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex gap-4 w-full md:w-auto">
                    <input type="text" placeholder="Search..." class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    
                    <select class="border border-gray-300 rounded-md px-4 py-2 bg-white text-gray-600 focus:outline-none">
                        <option>Filter Kategori</option>
                        <option>Pakaian</option>
                        <option>Olahraga</option>
                        <option>Elektronik</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition flex items-center gap-2">
                        Tambah Produk (+)
                    </button>
                    <button class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md font-medium transition">
                        Download Laporan (PDF)
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Gambar</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Nama Produk</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Kategori</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Harga</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Rating (Avg)</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Stok</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="h-12 w-12 bg-gray-200 rounded-md flex items-center justify-center text-gray-400 text-xs">
                                    Image
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $product['name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product['category'] }}</td>
                            <td class="px-6 py-4 text-gray-600">Rp {{ number_format($product['price'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-yellow-400 text-sm">
                                    @for($i = 0; $i < round($product['rating']); $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                    @for($i = 0; $i < (5 - round($product['rating'])); $i++)
                                        <i class="far fa-star text-gray-300"></i>
                                    @endfor
                                    <span class="ml-2 text-gray-500 text-xs font-semibold">{{ $product['rating'] }} ({{ $product['reviews'] }} Ulasan)</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $product['stock'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-3">
                                    <button class="text-gray-500 hover:text-blue-600" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-yellow-600" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-red-600" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
                    <div class="flex gap-1">
                        <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">Previous</button>
                        <button class="px-3 py-1 border rounded bg-blue-600 text-white">1</button>
                        <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">2</button>
                        <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">3</button>
                        <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">Next</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>