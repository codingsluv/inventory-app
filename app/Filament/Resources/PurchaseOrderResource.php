<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->required()
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->required()
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set){
                        $product = Product::find($state);
                        $set('price', $product ? $product->price : 0);
                    })
                    ->afterStateHydrated(function($state, callable $get, callable $set){
                        $product = Product::find($state);
                        $set('price', $product ? $product->price : 0);
                    }),
                    Forms\Components\TextInput::make('qty')
                    ->required()
                    ->numeric()
                    ->prefix('Qty Product')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set){
                        $price = $get('price');
                        $subTotal = $price * $state;
                        $totalPpn = $subTotal * 0.11;
                        $totalAmount = $subTotal + $totalPpn;

                        $set('total_price', $totalAmount);

                        $products = $get('products') ?? [];
                        $currentCount = count($products);

                        if($state > $currentCount){
                            for($i = $currentCount; $i < $state; $i++){
                                $products[] = ['name' => '', 'occupation' => '', 'email' => ''];
                            }
                        } else {
                            $products = array_slice($products, 0, $state);
                        }

                        $set('products', $products);
                    })
                    ->afterStateHydrated(function($state, callable $get, callable $set){
                        $price = $get('price');
                        $subTotal = $price * $state;
                        $totalPpn = $subTotal * 0.11;
                        $totalAmount = $subTotal + $totalPpn;

                        $set('total_price', $totalAmount);
                    })
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->required()
                        ->numeric()
                        ->prefix('IDR')
                        ->readOnly()
                        ->helperText('Harga sudah include PPN 11%'),
                Forms\Components\DatePicker::make('purchase_date')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name'),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('total_price'),
                Tables\Columns\TextColumn::make('purchase_date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
