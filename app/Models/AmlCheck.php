<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmlCheck extends Model
{
    protected $table = 'aml_checks';

    protected $fillable = [
        'user_id',
        'id_document',
        'proof_of_address',
        'verification_status',
        'checked_by',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user this AML check belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who checked this AML.
     */
    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    /**
     * Get all documents for this AML check.
     */
    public function documents()
    {
        return $this->hasMany(AmlDocument::class);
    }

    /**
     * Get ID documents for this AML check.
     */
    public function idDocuments()
    {
        return $this->hasMany(AmlDocument::class)->where('document_type', 'id_document');
    }

    /**
     * Get proof of address documents for this AML check.
     */
    public function proofOfAddressDocuments()
    {
        return $this->hasMany(AmlDocument::class)->where('document_type', 'proof_of_address');
    }

    /**
     * Get additional documents for this AML check.
     */
    public function additionalDocuments()
    {
        return $this->hasMany(AmlDocument::class)->where('document_type', 'additional');
    }
}
