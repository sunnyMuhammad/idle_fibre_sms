<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Middleware\TokenVerificationMiddleware;
use App\Http\Controllers\Frontend\RolePageController;
use App\Http\Controllers\Frontend\UserPageController;
use App\Http\Controllers\Frontend\VendorPageController;
use App\Http\Controllers\Frontend\ProductPageController;
use App\Http\Controllers\Frontend\CategoryPageController;
use App\Http\Controllers\Frontend\PurchasePageController;
use App\Http\Controllers\Frontend\RequisitionPageController;
use App\Http\Controllers\Frontend\FloorReceivePageController;
use App\Http\Controllers\Frontend\ProductIssuePageController;
use App\Http\Controllers\Frontend\RequisitionApproveReceivePageController;

//usrer login page
Route::get('/', [AuthController::class, 'loginPage'])->name('loginPage');

Route::middleware([TokenVerificationMiddleware::class])->group(function () {


    /**
     * =====================
     * Role Management Routes
     * =====================
     */
    Route::get('/list-role', [RolePageController::class, 'roleList'])->middleware('permission:list-role')->name('role.list');
    Route::get('/role-save-page', [RolePageController::class, 'roleSavePage'])->middleware('permission:update-role')->name('role.save.page');


    /**
     * =====================
     * User Management Routes
     * =====================
     */


    Route::get('/list-user', [UserPageController::class, 'listUser'])->middleware('permission:list-user')->name('user.list');
    Route::get('/user-save-page', [UserPageController::class, 'userSavePage'])->middleware('permission:create-user')->name('user.save.page');


    /**
     * =====================
     * Product Management Routes
     * =====================
     */
    Route::get('/product-stock-list-page', [ProductPageController::class, 'productStockListPage'])->name('product.stock.list.page');
    Route::get('/list-product-page', [ProductPageController::class, 'listProductPage'])->middleware('permission:list-product')->name('product.list');
    Route::get('/product-save-page', [ProductPageController::class, 'productSavePage'])->middleware('permission:create-product')->name('product.save.page');
    Route::get('/product-stock-report', [ProductPageController::class, 'productStockReport'])->middleware('permission:product-stock-report')->name('product.stock.report');
    Route::get('/product-list-report', [ProductPageController::class, 'productListReport'])->middleware('permission:product-report')->name('product.list.report');


    /**
     * =====================
     * Issue Product Routes
     * =====================
     */
    Route::get('/issue-product-list', [ProductIssuePageController::class, 'issueProductList'])->middleware('permission:list-issue-product')->name('product.issue.list');
    Route::get('/product-issue-page', [ProductIssuePageController::class, 'productIssuePage'])->middleware('permission:issue-product')->name('product.issue.page');


    /**
     * =====================
     * Category Routes
     * =====================
     */
    Route::get('/list-category', [CategoryPageController::class, 'listCategory'])->middleware('permission:list-category')->name('category.list');
    Route::get('/category-save-page', [CategoryPageController::class, 'categorySavePage'])->middleware('permission:create-category')->name('category.create');


    /**
     * =====================
     * Requisition Routes
     * =====================
     */
    Route::get('/list-requisition', [RequisitionPageController::class, 'listRequisition'])->middleware('permission:list-requisition')->name('requisition.list');
    Route::get('/requisition-save-page', [RequisitionPageController::class, 'requisitionSavePage'])->middleware('permission:create-requisition')->name('requisition.save.page');
    Route::get('/requisition-product-list', [RequisitionPageController::class, 'requisitionProductList'])->middleware('permission:receive-requisition')->name('requisition.product.list');


    // Requisition Approval & Received
    Route::get('/requisition-received-request-list', [RequisitionApproveReceivePageController::class, 'requisitionReceivedRequestList'])->middleware('permission:approve-reuisition-receive')->name('requisition.received.request.list');
    Route::get('/edit-requisition-request-page', [RequisitionApproveReceivePageController::class, 'editRequisitionRequestPage'])->middleware('permission:approve-reuisition-receive')->name('requisition.edit.request.page');


    /**
     * =====================
     * Floor Receive Routes
     * =====================
     */
    Route::get('/floor-receive-list', [FloorReceivePageController::class, 'floorReceiveList'])->middleware('permission:approve-floor-receive')->name('product.floor.receive.list');
    Route::get('/edit-floor-recieve-page', [FloorReceivePageController::class, 'editFloorRecievePage'])->middleware('permission:approve-floor-receive')->name('product.edit.floor.recieve.page');
    Route::get('/floor-recieve-page', [FloorReceivePageController::class, 'floorRecievePage'])->middleware('permission:receive-floor-receive')->name('product.floor.recieve.page');


    /**
     * =====================
     * Purchase Routes
     * =====================
     */
    Route::get('/list-purchase', [PurchasePageController::class, 'listPurchase'])->middleware('permission:list-purchase')->name('purchase.list');
    Route::get('/purchase-save-page', [PurchasePageController::class, 'purchaseSavePage'])->middleware('permission:create-purchase')->name('purchase.save.page');



    /**
     * =====================
     * Vendor Routes
     * =====================
     */
    Route::get('/list-vendor', [VendorPageController::class, 'listVendor'])->middleware('permission:list-vendor')->name('vendor.list');
    Route::get('/vendor-save-page', [VendorPageController::class, 'vendorSavePage'])->middleware('permission:create-vendor')->name('vendor.save.page');


    /**
     * =====================
     * Damage & Minimum Stock Product Routes
     * =====================
     */
    Route::get('/damage-product-list', [ProductPageController::class, 'damageProductList'])->middleware('permission:list-damage-product')->name('product.damage.list');
    Route::get('/minimum-product-list', [ProductPageController::class, 'minimumProductList'])->middleware('permission:list-minimum-product')->name('product.minimum.list');
});
