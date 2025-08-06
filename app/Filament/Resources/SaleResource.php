<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Detail Penjualan')
                        ->schema([
                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()->required()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\TextInput::make('contact_info'),
                                ]),
                            Forms\Components\DatePicker::make('date')->required()->default(now()),
                        ]),
                    Forms\Components\Wizard\Step::make('Item Penjualan')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->searchable()->required()->live()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            $product = Product::find($state);
                                            if ($product) $set('price', $product->harga_jual);
                                        }),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Jumlah')->numeric()->required()->default(1)->live()
                                        ->rules([
                                            fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $product = Product::find($get('product_id'));
                                                if ($product && $value > $product->stock) {
                                                    Notification::make()->title('Stok Tidak Cukup')->body("Stok {$product->name} hanya tersisa {$product->stock}.")->danger()->send();
                                                    $fail("Stok produk tidak mencukupi. Sisa stok: {$product->stock}");
                                                }
                                            },
                                        ]),
                                    Forms\Components\TextInput::make('price')->label('Harga Jual Satuan')->numeric()->prefix('Rp')->readOnly()->required(),
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
                Tables\Columns\TextColumn::make('customer.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            // 'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
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