<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\ProductsImport;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification; 

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importProducts')
                    ->label('Import Product')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->form([
                        FileUpload::make('attachment')
                            ->label('Upload Template')
                    ])
                    ->action(function (array $data) {
                        $file = public_path('storage/' .$data['attachment']);

                        try {
                            Excel::import(new ProductsImport, $file);
                            Notification::make()
                                ->title('Products Imported')
                                ->success()
                                ->send();
                        }  catch (\Exception $e) {
                            Notification::make()
                                ->title('Products Failed to Import')
                                ->danger()
                                ->send();
                        }

                    }),
            Action::make('Download Template')
                ->url(route('export-categories'))
                ->color('warning'),
            Actions\CreateAction::make(),
            
        ];
    }

    protected function setFlashMessage()
    {
        $error = Session::get('error');

        if ($error) {
            $this->notify($error, 'danger');
            Session::forget('error');
        }
    }
}
