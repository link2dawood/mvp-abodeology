<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RTDFGeneratorService
{
    /**
     * Generate RTDF file for a property.
     *
     * @param Property $property
     * @return string Path to generated RTDF file
     */
    public function generateForProperty(Property $property): string
    {
        try {
            // Map property data to RTDF format
            $rtdfData = $this->mapPropertyToRTDF($property);
            
            // Generate RTDF file content
            $rtdfContent = $this->generateRTDFContent($rtdfData);
            
            // Validate RTDF format
            $this->validateRTDF($rtdfContent);
            
            // Store file
            $fileName = 'property_' . $property->id . '.txt';
            $directory = 'feeds';
            $filePath = $directory . '/' . $fileName;
            
            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            // Ensure directory exists (only for local storage)
            if ($disk !== 's3') {
                $fullDirectoryPath = storage_path('app/public/' . $directory);
                if (!file_exists($fullDirectoryPath)) {
                    mkdir($fullDirectoryPath, 0755, true);
                }
            }
            
            // Save RTDF file
            Storage::disk($disk)->put($filePath, $rtdfContent);
            
            Log::info('RTDF file generated successfully for property ID: ' . $property->id);
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('RTDF generation error for property ID ' . $property->id . ': ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Map property data to RTDF format.
     *
     * @param Property $property
     * @return array
     */
    protected function mapPropertyToRTDF(Property $property): array
    {
        $property->load(['seller', 'photos', 'materialInformation']);
        
        // RTDF field mapping
        return [
            // Header fields
            'agent_ref' => $this->formatField('AGENT_REF', $property->id, 20),
            'branch_id' => $this->formatField('BRANCH_ID', config('rightmove.branch_id', 'ABODE001'), 20),
            'property_type' => $this->mapPropertyType($property->property_type),
            'status' => $this->mapStatus($property->status),
            
            // Address fields
            'address_line_1' => $this->formatField('ADDRESS_LINE_1', $this->extractAddressLine1($property->address), 100),
            'address_line_2' => $this->formatField('ADDRESS_LINE_2', $this->extractAddressLine2($property->address), 100),
            'town' => $this->formatField('TOWN', $this->extractTown($property->address), 50),
            'county' => $this->formatField('COUNTY', $this->extractCounty($property->address), 50),
            'postcode' => $this->formatField('POSTCODE', $property->postcode ?? '', 10),
            
            // Property details
            'bedrooms' => $this->formatField('BEDROOMS', $property->bedrooms ?? 0, 2, 'numeric'),
            'bathrooms' => $this->formatField('BATHROOMS', $property->bathrooms ?? 0, 2, 'numeric'),
            'reception_rooms' => $this->formatField('RECEPTION_ROOMS', $property->reception_rooms ?? 0, 2, 'numeric'),
            'price' => $this->formatField('PRICE', $property->asking_price ?? 0, 10, 'numeric'),
            'price_qualifier' => $this->mapPriceQualifier($property->pricing_notes),
            
            // Description
            'summary' => $this->formatField('SUMMARY', $this->generateSummary($property), 500),
            'description' => $this->formatField('DESCRIPTION', $this->generateDescription($property), 2000),
            
            // Features
            'features' => $this->formatField('FEATURES', $this->generateFeatures($property), 500),
            
            // Media
            'image_urls' => $this->mapImageUrls($property),
            
            // Contact details
            'agent_name' => $this->formatField('AGENT_NAME', config('rightmove.agent_name', 'Abodeology'), 100),
            'agent_phone' => $this->formatField('AGENT_PHONE', config('rightmove.agent_phone', ''), 20),
            'agent_email' => $this->formatField('AGENT_EMAIL', config('rightmove.agent_email', 'support@abodeology.co.uk'), 100),
            
            // Additional fields
            'tenure' => $this->mapTenure($property->tenure),
            'parking' => $this->mapParking($property->parking),
            'garden' => $this->mapGarden($property->garden_details),
            'epc_rating' => $this->formatField('EPC_RATING', $this->getEPCRating($property), 1),
            'epc_url' => $this->formatField('EPC_URL', $this->getEPCUrl($property), 255),
            
            // Timestamps
            'created_date' => $this->formatField('CREATED_DATE', $property->created_at->format('Y-m-d'), 10),
            'updated_date' => $this->formatField('UPDATED_DATE', $property->updated_at->format('Y-m-d'), 10),
        ];
    }
    
    /**
     * Generate RTDF file content from mapped data.
     *
     * @param array $data
     * @return string
     */
    protected function generateRTDFContent(array $data): string
    {
        // RTDF is a fixed-width format
        // Format: FIELD_NAME|VALUE|FIELD_NAME|VALUE|...
        $lines = [];
        
        // Header line
        $lines[] = 'RTDF|1.0|ABODEOLOGY|' . date('Y-m-d H:i:s');
        
        // Property data line
        $propertyLine = 'PROPERTY|';
        $propertyLine .= $data['agent_ref'] . '|';
        $propertyLine .= $data['branch_id'] . '|';
        $propertyLine .= $data['property_type'] . '|';
        $propertyLine .= $data['status'] . '|';
        $propertyLine .= $data['address_line_1'] . '|';
        $propertyLine .= $data['address_line_2'] . '|';
        $propertyLine .= $data['town'] . '|';
        $propertyLine .= $data['county'] . '|';
        $propertyLine .= $data['postcode'] . '|';
        $propertyLine .= $data['bedrooms'] . '|';
        $propertyLine .= $data['bathrooms'] . '|';
        $propertyLine .= $data['reception_rooms'] . '|';
        $propertyLine .= $data['price'] . '|';
        $propertyLine .= $data['price_qualifier'] . '|';
        $propertyLine .= $data['tenure'] . '|';
        $propertyLine .= $data['parking'] . '|';
        $propertyLine .= $data['garden'] . '|';
        $propertyLine .= $data['epc_rating'] . '|';
        $propertyLine .= $data['epc_url'] . '|';
        $propertyLine .= $data['created_date'] . '|';
        $propertyLine .= $data['updated_date'];
        $lines[] = $propertyLine;
        
        // Description line
        $lines[] = 'DESCRIPTION|' . $data['summary'] . '|' . $data['description'];
        
        // Features line
        if (!empty($data['features'])) {
            $lines[] = 'FEATURES|' . $data['features'];
        }
        
        // Images line
        if (!empty($data['image_urls'])) {
            $lines[] = 'IMAGES|' . implode('|', $data['image_urls']);
        }
        
        // Contact line
        $lines[] = 'CONTACT|' . $data['agent_name'] . '|' . $data['agent_phone'] . '|' . $data['agent_email'];
        
        // Footer line
        $lines[] = 'END|' . date('Y-m-d H:i:s');
        
        return implode("\r\n", $lines) . "\r\n";
    }
    
    /**
     * Validate RTDF file format.
     *
     * @param string $content
     * @return bool
     * @throws \Exception
     */
    protected function validateRTDF(string $content): bool
    {
        $lines = explode("\r\n", trim($content));
        
        // Must have at least header and property line
        if (count($lines) < 2) {
            throw new \Exception('RTDF file must have at least header and property lines');
        }
        
        // Check header format
        if (!str_starts_with($lines[0], 'RTDF|')) {
            throw new \Exception('RTDF file must start with RTDF header');
        }
        
        // Check property line format
        if (!str_starts_with($lines[1], 'PROPERTY|')) {
            throw new \Exception('RTDF file must have PROPERTY line');
        }
        
        // Check footer format
        $lastLine = end($lines);
        if (!str_starts_with($lastLine, 'END|')) {
            throw new \Exception('RTDF file must end with END footer');
        }
        
        return true;
    }
    
    /**
     * Format field value according to RTDF specifications.
     *
     * @param string $fieldName
     * @param mixed $value
     * @param int $maxLength
     * @param string $type
     * @return string
     */
    protected function formatField(string $fieldName, $value, int $maxLength, string $type = 'string'): string
    {
        if ($value === null) {
            return '';
        }
        
        if ($type === 'numeric') {
            return str_pad((string) $value, $maxLength, '0', STR_PAD_LEFT);
        }
        
        // String: trim and pad
        $value = (string) $value;
        $value = mb_substr($value, 0, $maxLength);
        return $value;
    }
    
    /**
     * Map property type to RTDF format.
     *
     * @param string|null $propertyType
     * @return string
     */
    protected function mapPropertyType(?string $propertyType): string
    {
        $mapping = [
            'house' => 'House',
            'flat' => 'Flat',
            'apartment' => 'Flat',
            'bungalow' => 'Bungalow',
            'cottage' => 'Cottage',
            'terraced_house' => 'Terraced House',
            'semi_detached_house' => 'Semi-Detached House',
            'detached_house' => 'Detached House',
            'maisonette' => 'Maisonette',
            'studio' => 'Studio',
            'penthouse' => 'Penthouse',
            'land' => 'Land',
            'commercial' => 'Commercial',
        ];
        
        return $mapping[strtolower($propertyType ?? '')] ?? 'House';
    }
    
    /**
     * Map property status to RTDF format.
     *
     * @param string|null $status
     * @return string
     */
    protected function mapStatus(?string $status): string
    {
        $mapping = [
            'live' => 'Available',
            'sstc' => 'Under Offer',
            'sold' => 'Sold',
            'withdrawn' => 'Withdrawn',
            'draft' => 'Draft',
        ];
        
        return $mapping[strtolower($status ?? '')] ?? 'Available';
    }
    
    /**
     * Map price qualifier.
     *
     * @param string|null $pricingNotes
     * @return string
     */
    protected function mapPriceQualifier(?string $pricingNotes): string
    {
        if (!$pricingNotes) {
            return 'Asking Price';
        }
        
        $pricingNotes = strtolower($pricingNotes);
        
        if (str_contains($pricingNotes, 'offers in the region of')) {
            return 'Offers in the Region of';
        } elseif (str_contains($pricingNotes, 'offers in excess of')) {
            return 'Offers in Excess of';
        } elseif (str_contains($pricingNotes, 'guide price')) {
            return 'Guide Price';
        }
        
        return 'Asking Price';
    }
    
    /**
     * Map tenure.
     *
     * @param string|null $tenure
     * @return string
     */
    protected function mapTenure(?string $tenure): string
    {
        if (!$tenure) {
            return 'Freehold';
        }
        
        $mapping = [
            'freehold' => 'Freehold',
            'leasehold' => 'Leasehold',
            'share_of_freehold' => 'Share of Freehold',
        ];
        
        return $mapping[strtolower($tenure)] ?? 'Freehold';
    }
    
    /**
     * Map parking.
     *
     * @param string|null $parking
     * @return string
     */
    protected function mapParking(?string $parking): string
    {
        if (!$parking) {
            return 'No';
        }
        
        $parking = strtolower($parking);
        
        if (str_contains($parking, 'garage')) {
            return 'Garage';
        } elseif (str_contains($parking, 'driveway')) {
            return 'Driveway';
        } elseif (str_contains($parking, 'off_street')) {
            return 'Off Street';
        } elseif (str_contains($parking, 'on_street')) {
            return 'On Street';
        }
        
        return 'Yes';
    }
    
    /**
     * Map garden.
     *
     * @param string|null $gardenDetails
     * @return string
     */
    protected function mapGarden(?string $gardenDetails): string
    {
        return $gardenDetails ? 'Yes' : 'No';
    }
    
    /**
     * Extract address line 1 from full address.
     *
     * @param string|null $address
     * @return string
     */
    protected function extractAddressLine1(?string $address): string
    {
        if (!$address) {
            return '';
        }
        
        $parts = explode(',', $address);
        return trim($parts[0] ?? '');
    }
    
    /**
     * Extract address line 2 from full address.
     *
     * @param string|null $address
     * @return string
     */
    protected function extractAddressLine2(?string $address): string
    {
        if (!$address) {
            return '';
        }
        
        $parts = explode(',', $address);
        return trim($parts[1] ?? '');
    }
    
    /**
     * Extract town from full address.
     *
     * @param string|null $address
     * @return string
     */
    protected function extractTown(?string $address): string
    {
        if (!$address) {
            return '';
        }
        
        $parts = explode(',', $address);
        return trim($parts[count($parts) - 2] ?? '');
    }
    
    /**
     * Extract county from full address.
     *
     * @param string|null $address
     * @return string
     */
    protected function extractCounty(?string $address): string
    {
        if (!$address) {
            return '';
        }
        
        $parts = explode(',', $address);
        $lastPart = trim(end($parts) ?? '');
        
        // Remove postcode if present
        $lastPart = preg_replace('/\b[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}\b/i', '', $lastPart);
        
        return trim($lastPart);
    }
    
    /**
     * Generate property summary.
     *
     * @param Property $property
     * @return string
     */
    protected function generateSummary(Property $property): string
    {
        $summary = [];
        
        if ($property->bedrooms) {
            $summary[] = $property->bedrooms . ' bedroom';
        }
        
        if ($property->property_type) {
            $summary[] = ucfirst(str_replace('_', ' ', $property->property_type));
        }
        
        if ($property->asking_price) {
            $summary[] = '£' . number_format($property->asking_price, 0);
        }
        
        return implode(' ', $summary);
    }
    
    /**
     * Generate property description.
     *
     * @param Property $property
     * @return string
     */
    protected function generateDescription(Property $property): string
    {
        $description = [];
        
        if ($property->materialInformation && $property->materialInformation->property_description) {
            $description[] = $property->materialInformation->property_description;
        } else {
            $description[] = $this->generateSummary($property) . ' located in ' . ($property->address ?? '');
        }
        
        if ($property->bedrooms) {
            $description[] = 'This property features ' . $property->bedrooms . ' bedroom(s)';
        }
        
        if ($property->bathrooms) {
            $description[] = $property->bathrooms . ' bathroom(s)';
        }
        
        if ($property->reception_rooms) {
            $description[] = $property->reception_rooms . ' reception room(s)';
        }
        
        if ($property->garden_details) {
            $description[] = 'Garden: ' . $property->garden_details;
        }
        
        if ($property->parking) {
            $description[] = 'Parking: ' . ucfirst(str_replace('_', ' ', $property->parking));
        }
        
        return implode('. ', $description) . '.';
    }
    
    /**
     * Generate features list.
     *
     * @param Property $property
     * @return string
     */
    protected function generateFeatures(Property $property): string
    {
        $features = [];
        
        if ($property->bedrooms) {
            $features[] = $property->bedrooms . ' Bedrooms';
        }
        
        if ($property->bathrooms) {
            $features[] = $property->bathrooms . ' Bathrooms';
        }
        
        if ($property->reception_rooms) {
            $features[] = $property->reception_rooms . ' Reception Rooms';
        }
        
        if ($property->parking) {
            $features[] = 'Parking';
        }
        
        if ($property->garden_details) {
            $features[] = 'Garden';
        }
        
        if ($property->tenure) {
            $features[] = ucfirst(str_replace('_', ' ', $property->tenure));
        }
        
        return implode(', ', $features);
    }
    
    /**
     * Map image URLs.
     *
     * @param Property $property
     * @return array
     */
    protected function mapImageUrls(Property $property): array
    {
        $urls = [];
        
        if ($property->photos && $property->photos->count() > 0) {
            $baseUrl = config('app.url');
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            foreach ($property->photos->take(20) as $photo) { // Rightmove allows up to 20 images
                $url = Storage::disk($disk)->url($photo->file_path);
                
                // If relative URL, make absolute
                if (!str_starts_with($url, 'http')) {
                    $url = $baseUrl . '/' . ltrim($url, '/');
                }
                
                $urls[] = $url;
            }
        }
        
        return $urls;
    }
    
    /**
     * Get EPC rating.
     *
     * @param Property $property
     * @return string
     */
    protected function getEPCRating(Property $property): string
    {
        // Check if EPC document exists
        $epcDocument = $property->documents()
            ->where('document_type', 'epc')
            ->first();
        
        if ($epcDocument) {
            // In production, would extract rating from EPC document
            // For now, return placeholder
            return 'C';
        }
        
        return '';
    }
    
    /**
     * Get EPC URL.
     *
     * @param Property $property
     * @return string
     */
    protected function getEPCUrl(Property $property): string
    {
        $epcDocument = $property->documents()
            ->where('document_type', 'epc')
            ->first();
        
        if ($epcDocument) {
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            $url = Storage::disk($disk)->url($epcDocument->file_path);
            
            // If relative URL, make absolute
            if (!str_starts_with($url, 'http')) {
                $url = config('app.url') . '/' . ltrim($url, '/');
            }
            
            return $url;
        }
        
        return '';
    }
}

