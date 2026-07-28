<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Hiển thị danh sách nhà cung cấp.
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.pages.suppliers', compact('suppliers'));
    }

    // Thêm nhà cung cấp mới.
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Supplier::create($data);

        return back()->with('success', 'Thêm nhà cung cấp thành công.');
    }

    // Cập nhật thông tin nhà cung cấp.
    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validatedData($request);
        $supplier->update($data);

        return back()->with('success', 'Cập nhật nhà cung cấp thành công.');
    }

    // Xóa nhà cung cấp nếu chưa phát sinh chứng từ.
    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists()
            || $supplier->importReceipts()->exists()
            || $supplier->damageSlips()->exists()
            || $supplier->warehouseStocks()->exists()) {
            return back()->with('error', 'Không thể xóa nhà cung cấp đã có chứng từ nhập hàng.');
        }

        $supplier->delete();

        return back()->with('success', 'Xóa nhà cung cấp thành công.');
    }

    // Kiểm tra dữ liệu nhà cung cấp trước khi lưu.
    private function validatedData($request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);
    }
}
