<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Products;
use Illuminate\Validation\Rule;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-storefront';
    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 100;

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('id', 'desc'))
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Product::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->afterStateUpdated(function (Closure $set) {
                                        $set('slug', Product::generateUniqueSlug($state));
                                    })
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('barcode')
                                    ->label('Barcode')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stok')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp.'),
                               
                            ])

                       

                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                           
                                Forms\Components\FileUpload::make('image')
                                    ->image(),
                                Forms\Components\Toggle::make('is_active')
                                    ->required(),
                            ])

                    ])
               
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               
                Tables\Columns\ImageColumn::make('image'),
               
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Product $record): string => ($record->category) ? $record->category->name : '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id')->label('SKU/ID'),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->color(static function ($state): string {
                        if ($state < 10) {
                            return 'danger';
                        } elseif ($state < 20) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
