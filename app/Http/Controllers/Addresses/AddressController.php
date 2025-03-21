<?php

declare(strict_types=1);

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Addresses\CreateAddressRequest;
use App\Http\Requests\Addresses\DeleteAddressRequest;
use App\Http\Requests\Addresses\UpdateAddressRequest;
use App\Http\Resources\Addresses\AddressResource;
use App\Http\Resources\Addresses\AddressResourceCollection;
use App\Models\Users\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function getUserAddresses(Request $request): AddressResourceCollection
    {
        $addresses = Address::where(Address::USER_ID, $request->user()->id)
            ->orderBy(Address::IS_DEFAULT, 'desc')
            ->orderBy(Address::CREATED_AT, 'desc')
            ->get();

        return new AddressResourceCollection($addresses);
    }

    public function createAddress(CreateAddressRequest $request): AddressResource
    {
        $isDefault = $request->getIsDefault();

        DB::transaction(function () use ($request, $isDefault) {
            if ($isDefault) {
                Address::where(Address::USER_ID, $request->user()->getId())
                    ->where(Address::TYPE, $request->getType())
                    ->update([Address::IS_DEFAULT => false]);
            }
        });

        $address = Address::create([
            Address::USER_ID => $request->user()->getId(),
            Address::NAME => $request->getName(),
            Address::PHONE => $request->getPhone(),
            Address::STREET_ADDRESS => $request->getStreetAddress(),
            Address::APARTMENT => $request->getApartment(),
            Address::CITY => $request->getCity(),
            Address::STATE => $request->getState(),
            Address::POSTAL_CODE => $request->getPostalCode(),
            Address::COUNTRY => $request->getCountry(),
            Address::IS_DEFAULT => $isDefault,
            Address::TYPE => $request->getType(),
        ]);

        return new AddressResource($address);
    }

    public function getAddressById(int $addressId, Request $request): AddressResource|JsonResponse
    {
        $address = Address::where(Address::ID, $addressId)
            ->where(Address::USER_ID, $request->user()->getId())
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return new AddressResource($address);
    }

    public function updateAddress(UpdateAddressRequest $request): AddressResource|JsonResponse
    {
        $address = Address::where(Address::ID, $request->getAddressId())
            ->where(Address::USER_ID, $request->user()->getId())
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $isDefault = $request->getIsDefault();

        DB::transaction(function () use ($request, $isDefault, $address) {
            if ($isDefault) {
                Address::where(Address::USER_ID, $request->user()->getId())
                    ->where(Address::ID, '!=', $address->getId())
                    ->where(Address::TYPE, $request->getType())
                    ->update([Address::IS_DEFAULT => false]);
            }

            $address->update([
                Address::NAME => $request->getName(),
                Address::PHONE => $request->getPhone(),
                Address::STREET_ADDRESS => $request->getStreetAddress(),
                Address::APARTMENT => $request->getApartment(),
                Address::CITY => $request->getCity(),
                Address::STATE => $request->getState(),
                Address::POSTAL_CODE => $request->getPostalCode(),
                Address::COUNTRY => $request->getCountry(),
                Address::IS_DEFAULT => $isDefault,
                Address::TYPE => $request->getType(),
            ]);
        });

        return new AddressResource($address->fresh());
    }

    public function deleteAddress(DeleteAddressRequest $request): JsonResponse
    {
        $address = Address::where(Address::ID, $request->getAddressId())
            ->where(Address::USER_ID, $request->user()->getId())
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json([], 204);
    }

    public function setDefaultAddress(int $addressId, Request $request): AddressResource|JsonResponse
    {
        $address = Address::where(Address::ID, $addressId)
            ->where(Address::USER_ID, $request->user()->getId())
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        DB::transaction(function () use ($request, $address) {
            Address::where(Address::USER_ID, $request->user()->getId())
                ->where(Address::ID, '!=', $address->getId())
                ->where(Address::TYPE, $address->getType())
                ->update([Address::IS_DEFAULT => false]);

            $address->update([
                Address::IS_DEFAULT => true
            ]);
        });

        return new AddressResource($address->fresh());
    }
}
