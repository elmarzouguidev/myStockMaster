@extends('layouts.app')

@section('title', 'Create Currency')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('currencies.index') }}">{{__('Currencies')}}</a></li>
        <li class="breadcrumb-item active">{{__('Add')}}</li>
    </ol>
@endsection

@section('content')
    <div class="container px-4 mx-auto">
        <form action="{{ route('currencies.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="w-full px-4">
                    @include('utils.alerts')
                    <div class="mb-4">
                        <button class="block uppercase mx-auto shadow bg-indigo-800 hover:bg-indigo-700 focus:shadow-outline focus:outline-none text-white text-xs py-3 px-10 rounded">Create Currency <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="w-full px-4">
                    <x-card>
                        <div class="p-4">
                            <div class="flex flex-wrap -mx-1">
                                <div class="xl:w-1/3 lg:w-1/2 sm:w-full px-4">
                                    <div class="mb-4">
                                        <label for="currency_name">{{__('Currency Name')}} <span class="text-red-500">*</span></label>
                                        <input type="text" class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded" name="currency_name" required>
                                    </div>
                                </div>
                                <div class="xl:w-1/3 lg:w-1/2 sm:w-full px-4">
                                    <div class="mb-4">
                                        <label for="code">{{__('Currency Code')}} <span class="text-red-500">*</span></label>
                                        <input type="text" class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded" name="code" required>
                                    </div>
                                </div>
                            
                                <div class="w-full md:w-1/3 px-4 mb-4 md:mb-0">
                                    <div class="mb-4">
                                        <label for="symbol">{{__('Symbol')}} <span class="text-red-500">*</span></label>
                                        <input type="text" class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded" name="symbol" required>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/3 px-4 mb-4 md:mb-0">
                                    <div class="mb-4">
                                        <label for="thousand_separator">{{__('Thousand Separator')}} <span class="text-red-500">*</span></label>
                                        <input type="text" class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded" name="thousand_separator" required>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/3 px-4 mb-4 md:mb-0">
                                    <div class="mb-4">
                                        <label for="decimal_separator">{{__('Decimal Separator')}} <span class="text-red-500">*</span></label>
                                        <input type="text" class="block w-full px-4 py-3 mb-2 text-sm placeholder-gray-500 bg-white border rounded" name="decimal_separator" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </form>
    </div>
@endsection

