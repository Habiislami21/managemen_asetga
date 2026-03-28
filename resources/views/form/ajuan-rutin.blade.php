<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BMIPusat-Aset Online</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3"></script>
    <style>
        body {
            background-image: url("{{ asset('img/background-2.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;     
        }
        .table-bordered {
            border-collapse: collapse;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-bordered th {
            background-color: #6c5ce7;
            color: white;
        }
        .table-bordered tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #dfe6e9 !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6" id="app">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">Form Pengajuan Rutin</h1>
            
            <form action="{{ route('ajuan-rutin.store') }}" method="POST">
                @csrf
                
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2" for="nama_spa">Nama SPA:</label>
                        <input type="text" name="nama_spa" id="nama_spa" class="w-full border rounded-md p-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2" for="divisi_id">Divisi:</label>
                        <select name="divisi_id" id="divisi_id" class="w-full border rounded-md p-2" required>
                            <option value="">-- Pilih Divisi --</option>
                            <!-- Loop through divisions from database -->
                             @foreach ($divisis as $divisi)
                            <option value="{{ $divisi->id }}"
                                {{old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->divisi}}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2" for="nomor_telp">Nomor Telepon:</label>
                        <input type="text" name="nomor_telp" id="nomor_telp" class="w-full border rounded-md p-2" required>
                    </div>
                </div>
                
                <div class="overflow-x-auto mb-6">
                    <table class="w-full table-bordered">
                        <thead>
                            <tr class="bg-purple-600 text-white">
                                <th class="w-10">No</th>
                                <th>Uraian</th>
                                <th>Kategori</th>
                                <th class="w-16">Qty</th>
                                <th class="w-24">Satuan</th>
                                <th class="w-32">Harga Satuan</th>
                                <th class="w-32">Jumlah</th>
                                <th class="w-64">Keterangan</th>
                                <th class="w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="index">
                                <td class="text-center">@{{ index + 1 }}</td>
                                <td>
                                    <input type="text" v-model="item.barang_ajuan" :name="`items[${index}][barang_ajuan]`" class="w-full border-0 bg-transparent" required>
                                </td>
                                <td>
                                    <select v-model="item.kategori_barang" :name="`items[${index}][kategori_barang]`" class="w-full border-0 bg-transparent" required>
                                        <option value="RTK">RTK</option>
                                        <option value="ATK">ATK</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" v-model="item.banyak_barang" :name="`items[${index}][banyak_barang]`" class="w-full border-0 bg-transparent" min="1" required @input="calculateItemTotal(index)">
                                </td>
                                <td>
                                    <select v-model="item.satuan" :name="`items[${index}][satuan]`" class="w-full border-0 bg-transparent" required>
                                        <option value="bulan">bulan</option>
                                        <option value="pcs">pcs</option>
                                        <option value="pack">pack</option>
                                        <option value="kg">kg</option>
                                        <option value="rim">rim</option>
                                        <option value="kotak">kotak</option>
                                        <option value="bungkus">bungkus</option>
                                        <option value="botol">botol</option>
                                        <option value="dus">dus</option>
                                        <option value="lusin">lusin</option>
                                        <option value="set">set</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span class="mr-1">Rp</span>
                                        <input type="number" v-model="item.harga" :name="`items[${index}][harga]`" class="w-full border-0 bg-transparent" min="0" required @input="calculateItemTotal(index)">
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span class="mr-1">Rp</span>
                                        <input type="number" v-model="item.total" :name="`items[${index}][total]`" class="w-full border-0 bg-transparent" readonly>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" v-model="item.keterangan" :name="`items[${index}][keterangan]`" class="w-full border-0 bg-transparent">
                                </td>
                                <td class="text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" v-if="items.length > 1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="6" class="text-right font-bold">TOTAL</td>
                                <td>
                                    <div class="flex items-center">
                                        <span class="mr-1">Rp</span>
                                        <input type="number" v-model="totalAmount" name="total_amount" class="w-full border-0 bg-transparent font-bold" readonly>
                                    </div>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-between mb-6">
                    <button type="button" @click="addItem" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        + Tambah Item
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        Simpan Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const { createApp, ref, computed, onMounted } = Vue;
              
    createApp({
        setup() {
            const items = ref([]);
            const totalAmount = ref(0);
            
            // Initialize with 1 empty row
            const initializeItems = () => {
                items.value = [];
                addEmptyItem();
            };
            
            const addEmptyItem = () => {
                items.value.push({
                    barang_ajuan: '',
                    kategori_barang: 'RTK',
                    banyak_barang: 1,
                    satuan: 'bulan',
                    harga: 0,
                    total: 0,
                    keterangan: ''
                });
            };
            
            const addItem = () => {
                addEmptyItem();
            };
            
            const removeItem = (index) => {
                if (items.value.length > 1) {
                    items.value.splice(index, 1);
                    calculateTotal();
                }
            };
            
            const calculateItemTotal = (index) => {
                const item = items.value[index];
                // Ensure both values are treated as numbers
                const qty = Number(item.banyak_barang) || 0;
                const price = Number(item.harga) || 0;
                
                // Calculate total and apply to the item
                item.total = qty * price;
                
                // Recalculate grand total
                calculateTotal();
            };
            
            const calculateTotal = () => {
                totalAmount.value = items.value.reduce((sum, item) => {
                    return sum + (Number(item.total) || 0);
                }, 0);
            };
            
            // Add a method to handle form submission
            const handleSubmit = (event) => {
                // Check if the form is valid before submission
                if (items.value.some(item => !item.barang_ajuan || !item.banyak_barang || !item.harga)) {
                    alert('Harap lengkapi semua data item sebelum menyimpan.');
                    event.preventDefault();
                    return false;
                }
                
                // Everything is valid, let the form submit
                return true;
            };
            
            onMounted(() => {
                initializeItems();
            });
            
            return {
                items,
                addItem,
                removeItem,
                calculateItemTotal,
                totalAmount,
                handleSubmit
            };
        }
    }).mount('#app');
       
    </script>
</body>
</html>