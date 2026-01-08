<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Shipments table indexes
        Schema::table('shipments', function (Blueprint $table) {
            $table->index('status', 'idx_shipments_status');
            $table->index('company_id', 'idx_shipments_company_id');
            $table->index('created_at', 'idx_shipments_created_at');
            $table->index(['status', 'company_id'], 'idx_shipments_status_company');
            $table->index('driver_id', 'idx_shipments_driver_id');
            $table->index('truck_id', 'idx_shipments_truck_id');
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status', 'idx_invoices_status');
            $table->index('payment_type', 'idx_invoices_payment_type');
            $table->index('collected_at', 'idx_invoices_collected_at');
            $table->index(['status', 'payment_type'], 'idx_invoices_status_payment');
            $table->index('created_at', 'idx_invoices_created_at');
        });

        // Claims table indexes
        Schema::table('claims', function (Blueprint $table) {
            $table->index('status', 'idx_claims_status');
            $table->index('payment_type', 'idx_claims_payment_type');
            $table->index('collected_at', 'idx_claims_collected_at');
        });

        // Packages table indexes
        Schema::table('packages', function (Blueprint $table) {
            $table->index('shipment_id', 'idx_packages_shipment_id');
        });

        // Destinations table composite index
        Schema::table('destinations', function (Blueprint $table) {
            $table->index(
                ['company_id', 'from_area', 'to_area', 'truck_type_id'], 
                'idx_destinations_route'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex('idx_shipments_status');
            $table->dropIndex('idx_shipments_company_id');
            $table->dropIndex('idx_shipments_created_at');
            $table->dropIndex('idx_shipments_status_company');
            $table->dropIndex('idx_shipments_driver_id');
            $table->dropIndex('idx_shipments_truck_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_status');
            $table->dropIndex('idx_invoices_payment_type');
            $table->dropIndex('idx_invoices_collected_at');
            $table->dropIndex('idx_invoices_status_payment');
            $table->dropIndex('idx_invoices_created_at');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropIndex('idx_claims_status');
            $table->dropIndex('idx_claims_payment_type');
            $table->dropIndex('idx_claims_collected_at');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex('idx_packages_shipment_id');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropIndex('idx_destinations_route');
        });
    }
};















