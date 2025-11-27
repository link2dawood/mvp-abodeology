<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\HomecheckReport;
use App\Models\HomecheckData;
use App\Models\PropertyInstruction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyWithRoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or use the seller user from DatabaseSeeder
        $seller = User::where('email', 'seller@abodeology.co.uk')->first();
        
        if (!$seller) {
            $this->command->error('Seller user not found. Please run DatabaseSeeder first.');
            return;
        }

        // Sample properties with different types
        $properties = [
            [
                'address' => '123 Oak Street, London',
                'postcode' => 'SW1A 1AA',
                'property_type' => 'detached',
                'bedrooms' => 4,
                'bathrooms' => 2,
                'reception_rooms' => 2,
                'parking' => 'driveway',
                'tenure' => 'freehold',
                'asking_price' => 650000,
                'rooms' => [
                    'Living Room' => 3,
                    'Kitchen' => 2,
                    'Master Bedroom' => 2,
                    'Bathroom' => 1,
                    'Garden' => 2,
                ],
            ],
            [
                'address' => '45 Maple Avenue, Manchester',
                'postcode' => 'M1 1AB',
                'property_type' => 'semi',
                'bedrooms' => 3,
                'bathrooms' => 1,
                'reception_rooms' => 1,
                'parking' => 'on_street',
                'tenure' => 'freehold',
                'asking_price' => 285000,
                'rooms' => [
                    'Living Room' => 2,
                    'Kitchen' => 2,
                    'Bedroom 1' => 1,
                    'Bedroom 2' => 1,
                    'Bathroom' => 1,
                ],
            ],
            [
                'address' => '78 Elm Road, Birmingham',
                'postcode' => 'B1 1CD',
                'property_type' => 'terraced',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'reception_rooms' => 1,
                'parking' => 'none',
                'tenure' => 'leasehold',
                'lease_years_remaining' => 85,
                'ground_rent' => 250,
                'asking_price' => 195000,
                'rooms' => [
                    'Living Room' => 2,
                    'Kitchen' => 1,
                    'Bedroom' => 1,
                    'Bathroom' => 1,
                ],
            ],
            [
                'address' => '12 Pine Close, Bristol',
                'postcode' => 'BS1 1EF',
                'property_type' => 'flat',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'reception_rooms' => 1,
                'parking' => 'allocated',
                'tenure' => 'leasehold',
                'lease_years_remaining' => 95,
                'service_charge' => 1200,
                'asking_price' => 175000,
                'rooms' => [
                    'Living Room' => 2,
                    'Kitchen' => 1,
                    'Bedroom' => 1,
                    'Bathroom' => 1,
                ],
            ],
            [
                'address' => '56 Cedar Drive, Leeds',
                'postcode' => 'LS1 1GH',
                'property_type' => 'bungalow',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'reception_rooms' => 2,
                'parking' => 'garage',
                'tenure' => 'freehold',
                'asking_price' => 425000,
                'rooms' => [
                    'Living Room' => 3,
                    'Kitchen' => 2,
                    'Master Bedroom' => 2,
                    'Bedroom 2' => 1,
                    'Bathroom' => 1,
                    'Garden' => 2,
                ],
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($properties as $propertyData) {
                // Extract rooms data
                $rooms = $propertyData['rooms'];
                unset($propertyData['rooms']);

                // Create property with live status
                $property = Property::create([
                    'seller_id' => $seller->id,
                    'status' => 'live',
                    'solicitor_details_completed' => true,
                    'solicitor_name' => 'John Smith',
                    'solicitor_firm' => 'Smith & Associates',
                    'solicitor_email' => 'john.smith@lawfirm.co.uk',
                    'solicitor_phone' => '+44 20 1234 5678',
                    ...$propertyData,
                ]);

                // Create instruction record (required for live properties)
                PropertyInstruction::create([
                    'property_id' => $property->id,
                    'seller_id' => $seller->id,
                    'status' => 'signed',
                    'signed_at' => now()->subDays(rand(5, 30)),
                    'fee_percentage' => 1.5,
                    'declaration_accurate' => true,
                    'declaration_legal_entitlement' => true,
                    'declaration_immediate_marketing' => true,
                    'declaration_terms' => true,
                    'declaration_homecheck' => true,
                    'seller1_name' => $seller->name,
                    'seller1_date' => now()->subDays(rand(5, 30)),
                ]);

                // Create HomeCheck report
                $homecheckReport = HomecheckReport::create([
                    'property_id' => $property->id,
                    'status' => 'completed',
                    'scheduled_by' => $seller->id,
                    'scheduled_date' => now()->subDays(rand(10, 20)),
                    'completed_by' => $seller->id,
                    'completed_at' => now()->subDays(rand(5, 15)),
                    'provider' => 'Abodeology',
                ]);

                // Create room images (HomecheckData)
                foreach ($rooms as $roomName => $imageCount) {
                    for ($i = 1; $i <= $imageCount; $i++) {
                        // Create fake image path (in production, these would be actual uploaded images)
                        $imagePath = 'homechecks/' . $property->id . '/rooms/' . $roomName . '/photos/image_' . $i . '.jpg';
                        
                        HomecheckData::create([
                            'property_id' => $property->id,
                            'homecheck_report_id' => $homecheckReport->id,
                            'room_name' => $roomName,
                            'image_path' => $imagePath,
                            'is_360' => false,
                            'created_at' => now()->subDays(rand(5, 15)),
                        ]);
                    }
                }

                $this->command->info("✅ Created property: {$property->address} with " . count($rooms) . " rooms");
            }

            DB::commit();
            
            $this->command->info('');
            $this->command->info('✅ Successfully created ' . count($properties) . ' live properties with rooms!');
            $this->command->info('   All properties are set to "live" status and associated with seller@abodeology.co.uk');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating properties: ' . $e->getMessage());
            throw $e;
        }
    }
}

