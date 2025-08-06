<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SaleResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSalesTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '5 Penjualan Terakhir';

    public function table(Table $table): Table
    {
        return $table
            ->query(SaleResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('d M Y'),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR'),
            ])
            ->actions([
                Tables\Actions\Action::make('Lihat')->url(fn ($record): string => SaleResource::getUrl('view', ['record' => $record])),
            ]);
    }
}