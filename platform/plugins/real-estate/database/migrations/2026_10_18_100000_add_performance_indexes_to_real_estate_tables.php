<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive, read-performance indexes only - no unique constraints, no
 * foreign keys, no column changes. None of these alter query results or
 * application behavior, only how fast MySQL can satisfy the WHERE/JOIN/
 * ORDER BY clauses the app already issues against these columns.
 */
class AddPerformanceIndexesToRealEstateTables extends Migration
{
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->index(['moderation_status', 'date_published', 'status'], 're_properties_moderation_published_status_index');
            $table->index('category_id', 're_properties_category_id_index');
            $table->index('city_id', 're_properties_city_id_index');
            $table->index('city_area_id', 're_properties_city_area_id_index');
            $table->index('project_id', 're_properties_project_id_index');
            $table->index('country_id', 're_properties_country_id_index');
            $table->index('state_id', 're_properties_state_id_index');
            $table->index(['author_type', 'author_id'], 're_properties_author_type_author_id_index');
            $table->index('member_id', 're_properties_member_id_index');
            $table->index('created_at', 're_properties_created_at_index');
            $table->index('is_deleted', 're_properties_is_deleted_index');
        });

        Schema::table('re_accounts', function (Blueprint $table) {
            $table->index('confirmed_at', 're_accounts_confirmed_at_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('user_id', 'transactions_user_id_index');
            $table->index('account_id', 'transactions_account_id_index');
            $table->index('payment_id', 'transactions_payment_id_index');
        });

        Schema::table('re_consults', function (Blueprint $table) {
            $table->index('project_id', 're_consults_project_id_index');
            $table->index('property_id', 're_consults_property_id_index');
            $table->index('status', 're_consults_status_index');
        });

        Schema::table('favourite_properties', function (Blueprint $table) {
            // Plain (non-unique) index: speeds up isFavourite()/
            // getFavouritePropertiesByUser() without risking a migration
            // failure if pre-existing duplicate rows already exist.
            $table->index(['user_id', 'user_type', 'property_id'], 'favourite_properties_user_property_index');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->index('agent_id', 'ratings_agent_id_index');
            $table->index('property_id', 'ratings_property_id_index');
            $table->index('user_id', 'ratings_user_id_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['commentable_type', 'commentable_id'], 'comments_commentable_type_id_index');
        });

        Schema::table('buyer', function (Blueprint $table) {
            $table->index('property_id', 'buyer_property_id_index');
            $table->index('seller_id', 'buyer_seller_id_index');
            $table->index('agent_id', 'buyer_agent_id_index');
        });

        Schema::table('category_documents', function (Blueprint $table) {
            $table->index(['category_id', 'document_id'], 'category_documents_category_document_index');
        });

        Schema::table('city_area', function (Blueprint $table) {
            $table->index('parent_id', 'city_area_parent_id_index');
        });

        Schema::table('re_categories', function (Blueprint $table) {
            $table->index('parent_id', 're_categories_parent_id_index');
        });
    }

    public function down()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropIndex('re_properties_moderation_published_status_index');
            $table->dropIndex('re_properties_category_id_index');
            $table->dropIndex('re_properties_city_id_index');
            $table->dropIndex('re_properties_city_area_id_index');
            $table->dropIndex('re_properties_project_id_index');
            $table->dropIndex('re_properties_country_id_index');
            $table->dropIndex('re_properties_state_id_index');
            $table->dropIndex('re_properties_author_type_author_id_index');
            $table->dropIndex('re_properties_member_id_index');
            $table->dropIndex('re_properties_created_at_index');
            $table->dropIndex('re_properties_is_deleted_index');
        });

        Schema::table('re_accounts', function (Blueprint $table) {
            $table->dropIndex('re_accounts_confirmed_at_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_id_index');
            $table->dropIndex('transactions_account_id_index');
            $table->dropIndex('transactions_payment_id_index');
        });

        Schema::table('re_consults', function (Blueprint $table) {
            $table->dropIndex('re_consults_project_id_index');
            $table->dropIndex('re_consults_property_id_index');
            $table->dropIndex('re_consults_status_index');
        });

        Schema::table('favourite_properties', function (Blueprint $table) {
            $table->dropIndex('favourite_properties_user_property_index');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex('ratings_agent_id_index');
            $table->dropIndex('ratings_property_id_index');
            $table->dropIndex('ratings_user_id_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_commentable_type_id_index');
        });

        Schema::table('buyer', function (Blueprint $table) {
            $table->dropIndex('buyer_property_id_index');
            $table->dropIndex('buyer_seller_id_index');
            $table->dropIndex('buyer_agent_id_index');
        });

        Schema::table('category_documents', function (Blueprint $table) {
            $table->dropIndex('category_documents_category_document_index');
        });

        Schema::table('city_area', function (Blueprint $table) {
            $table->dropIndex('city_area_parent_id_index');
        });

        Schema::table('re_categories', function (Blueprint $table) {
            $table->dropIndex('re_categories_parent_id_index');
        });
    }
}
