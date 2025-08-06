<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pembelian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Detail Pembelian')
                        ->schema([
                            Forms\Components\Select::make('supplier_id')
                                ->relationship('supplier', 'name')
                                ->searchable()->required()->live()
                                ->afterStateUpdated(fn (Set $set) => $set('items', [])),
                            Forms\Components\DatePicker::make('date')->required()->default(now()),
                        ]),
                    Forms\Components\Wizard\Step::make('Item Produk')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Produk')
                                        ->options(function (Get $get): Collection {
                                            $supplier = Supplier::find($get('../../supplier_id'));
                                            return $supplier ? $supplier->products()->pluck('name', 'products.id') : collect();
                                        })
                                        ->required()->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            $product = Product::find($state);
                                            $supplier = Supplier::find($get('../../supplier_id'));
                                            if ($product && $supplier) {
                                                $price = $supplier->products()->where('product_id', $product->id)->first()->pivot->price;
                                                $set('price', $price);
                                            }
                                        }),
                                    Forms\Components\TextInput::make('quantity')->label('Jumlah')->numeric()->required()->default(1),
                                    Forms\Components\TextInput::make('price')->label('Harga Beli Satuan')->numeric()->prefix('Rp')->readOnly()->required(),
                                ])
                                ->columns(3)->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                                ->deleteAction(fn (Forms\Components\Actions\Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set))),
                        ]),
                    Forms\Components\Wizard\Step::make('Ringkasan')
                        ->schema([
                            Forms\Components\Placeholder::make('total_amount_placeholder')
                                ->label('Total Keseluruhan')
                                ->content(fn (Get $get) => 'Rp ' . number_format($get('total_amount') ?? 0, 0, ',', '.')),
                            Forms\Components\Hidden::make('total_amount')
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            // 'view' => Pages\ViewPurchase::route('/{record}'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $total = collect($get('items'))->reduce(function ($carry, $item) {
            return $carry + (($item['price'] ?? 0) * ($item['quantity'] ?? 0));
        }, 0);
        
        $set('total_amount', $total);
    }
}