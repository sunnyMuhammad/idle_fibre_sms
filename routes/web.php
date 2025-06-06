<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\VendorController;
use App\Http\Middleware\ManagerMiddleware;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Middleware\ModeratorMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Controllers\RequisitionController;
use App\Http\Middleware\TokenVerificationMiddleware;




//user login
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/', [AuthController::class, 'loginPage'])->name('loginPage');

//user logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// access for all users
Route::middleware([TokenVerificationMiddleware::class])->group(function () {

    /**
     * =====================
     * Role Management Routes
     * =====================
     */
    Route::get('/list-role', [RoleController::class, 'roleList'])->middleware('permission:list-role')->name('role.list');
    Route::get('/role-save-page', [RoleController::class, 'roleSavePage'])->middleware('permission:update-role')->name('role.save.page');
    Route::post('/create-role', [RoleController::class, 'createRole'])->middleware('permission:create-role')->name('role.create');
    Route::post('/update-role', [RoleController::class, 'updateRole'])->middleware('permission:update-role')->name('role.update');
    Route::get('/delete-role', [RoleController::class, 'deleteRole'])->middleware('permission:delete-role')->name('role.delete');

    /**
     * =====================
     * User Management Routes
     * =====================
     */
    Route::get('/list-user', [UserController::class, 'listUser'])->middleware('permission:list-user')->name('user.list');
    Route::get('/user-save-page', [UserController::class, 'userSavePage'])->middleware('permission:create-user')->name('user.save.page');
    Route::post('/create-user', [UserController::class, 'createUser'])->middleware('permission:create-user')->name('user.create');
    Route::post('/update-user', [UserController::class, 'updateUser'])->middleware('permission:update-user')->name('user.update');
    Route::get('/delete-user', [UserController::class, 'deleteUser'])->middleware('permission:delete-user')->name('user.delete');

    /**
     * =====================
     * Product Management Routes
     * =====================
     */
    Route::get('/product-stock-list', [ProductController::class, 'productStockList'])->middleware('permission:list-product')->name('product.stock.list');
    Route::get('/list-product', [ProductController::class, 'listProduct'])->middleware('permission:list-product')->name('product.list');
    Route::post('/create-product', [ProductController::class, 'createProduct'])->middleware('permission:create-product')->name('product.create');
    Route::get('/product-save-page', [ProductController::class, 'productSavePage'])->middleware('permission:create-product')->name('product.save.page');
    Route::post('/update-product', [ProductController::class, 'updateProduct'])->middleware('permission:update-product')->name('product.update');
    Route::get('/delete-product', [ProductController::class, 'deleteProduct'])->middleware('permission:delete-product')->name('product.delete');

    /**
     * =====================
     * Issue Product Routes
     * =====================
     */
    Route::get('/issue-product-list', [ProductController::class, 'issueProductList'])->middleware('permission:list-issue-product')->name('product.issue.list');
    Route::post('/issue-product', [ProductController::class, 'issueProduct'])->middleware('permission:issue-product')->name('product.issue');
    Route::get('/product-issue-page', [ProductController::class, 'productIssuePage'])->middleware('permission:issue-product')->name('product.issue.page');

    /**
     * =====================
     * Damage & Minimum Stock Product Routes
     * =====================
     */
    Route::get('/damage-product-list', [ProductController::class, 'damageProductList'])->middleware('permission:list-damage-product')->name('product.damage.list');
    Route::get('/minimum-product-list', [ProductController::class, 'minimumProductList'])->middleware('permission:list-minimum-product')->name('product.minimum.list');

    /**
     * =====================
     * Category Routes
     * =====================
     */
    Route::get('/list-category', [CategoryController::class, 'listCategory'])->middleware('permission:list-category')->name('category.list');
    Route::post('/create-category', [CategoryController::class, 'createCategory'])->middleware('permission:create-category')->name('category.create');
    Route::post('/update-category', [CategoryController::class, 'updateCategory'])->middleware('permission:update-category')->name('category.update');
    Route::get('/category-save-page', [CategoryController::class, 'categorySavePage'])->middleware('permission:create-category')->name('category.save.page');
    Route::get('/delete-category', [CategoryController::class, 'deleteCategory'])->middleware('permission:delete-category')->name('category.delete');

    /**
     * =====================
     * Requisition Routes
     * =====================
     */
    Route::get('/list-requisition', [RequisitionController::class, 'listRequisition'])->middleware('permission:list-requisition')->name('requisition.list');
    Route::get('/requisition-save-page', [RequisitionController::class, 'requisitionSavePage'])->middleware('permission:create-requisition')->name('requisition.save.page');
    Route::post('/create-requisition', [RequisitionController::class, 'createRequisition'])->middleware('permission:create-requisition')->name('requisition.create');
    Route::get('/delete-requisition', [RequisitionController::class, 'deleteRequisition'])->middleware('permission:delete-requisition')->name('requisition.delete');

    // Requisition Approval & Received
    Route::get('/requisition-received-request-list', [RequisitionController::class, 'requisitionReceivedRequestList'])->middleware('permission:approve-requisition-receive')->name('requisition.received.request.list');
    Route::get('/requisition-approve-request', [RequisitionController::class, 'requisitionApproveRequest'])->middleware('permission:approve-requisition-receive')->name('requisition.approve.request');
    Route::get('/edit-requisition-request-page', [RequisitionController::class, 'editRequisitionRequestPage'])->middleware('permission:approve-requisition-receive')->name('requisition.edit.request.page');
    Route::post('/update-requisition-request', [RequisitionController::class, 'updateRequisitionRequest'])->middleware('permission:approve-requisition-receive')->name('requisition.update.request');
    Route::get('/requisition-product-list', [RequisitionController::class, 'requisitionProductList'])->middleware('permission:receive-requisition')->name('requisition.product.list');
    Route::post('/requisition-received-request', [RequisitionController::class, 'requisitionReceivedRequest'])->middleware('permission:receive-requisition')->name('requisition.received.request');

    /**
     * =====================
     * Floor Receive Routes
     * =====================
     */
    Route::get('/floor-receive-list', [ProductController::class, 'floorReceiveList'])->middleware('permission:approve-floor-receive')->name('product.floor.receive.list');
    Route::get('/approve-floor-recieve', [ProductController::class, 'approveFloorRecieve'])->middleware('permission:approve-floor-receive')->name('product.approve.floor.recieve');
    Route::get('/edit-floor-recieve-page', [ProductController::class, 'editFloorRecievePage'])->middleware('permission:approve-floor-receive')->name('product.edit.floor.recieve.page');
    Route::post('/update-floor-recieve', [ProductController::class, 'updateFloorReceive'])->middleware('permission:approve-floor-receive')->name('product.update.floor.receive');
    Route::get('/floor-recieve-page', [ProductController::class, 'floorRecievePage'])->middleware('permission:receive-floor-receive')->name('product.floor.recieve.page');
    Route::post('/floor-receive', [ProductController::class, 'floorReceive'])->middleware('permission:receive-floor-receive')->name('product.floor.receive');

    /**
     * =====================
     * Purchase Routes
     * =====================
     */
    Route::get('/list-purchase', [PurchaseController::class, 'listPurchase'])->middleware('permission:list-purchase')->name('purchase.list');
    Route::get('/purchase-save-page', [PurchaseController::class, 'purchaseSavePage'])->middleware('permission:create-purchase')->name('purchase.save.page');
    Route::post('/create-purchase', [PurchaseController::class, 'createPurchase'])->middleware('permission:create-purchase')->name('purchase.create');
    Route::post('/update-purchase', [PurchaseController::class, 'updatePurchase'])->middleware('permission:update-purchase')->name('purchase.update');
    Route::get('/delete-purchase', [PurchaseController::class, 'deletePurchase'])->middleware('permission:delete-purchase')->name('purchase.delete');

    /**
     * =====================
     * Vendor Routes
     * =====================
     */
    Route::get('/list-vendor', [VendorController::class, 'listVendor'])->middleware('permission:list-vendor')->name('vendor.list');
    Route::get('/vendor-save-page', [VendorController::class, 'vendorSavePage'])->middleware('permission:create-vendor')->name('vendor.save.page');
    Route::post('/create-vendor', [VendorController::class, 'createVendor'])->middleware('permission:create-vendor')->name('vendor.create');
    Route::post('/update-vendor', [VendorController::class, 'updateVendor'])->middleware('permission:update-vendor')->name('vendor.update');
    Route::get('/delete-vendor', [VendorController::class, 'deleteVendor'])->middleware('permission:delete-vendor')->name('vendor.delete');

});
