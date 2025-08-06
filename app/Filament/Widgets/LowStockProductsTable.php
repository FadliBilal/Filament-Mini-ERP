<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LowStockProductsTable extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Produk dengan Stok Rendah (<=10)';

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::where('stock', '<=', 10))
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Produk'),
                Tables\Columns\TextColumn::make('stock')->label('Sisa Stok')->badge()->color('danger'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()->isAdmin();
    }
}