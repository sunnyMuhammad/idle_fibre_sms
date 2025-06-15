<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\RequisitionController;
use App\Http\Controllers\Backend\FloorReceiveController;
use App\Http\Controllers\Backend\ProductIssueController;
use App\Http\Controllers\Backend\RequisitionApproveReceiveController;
use App\Http\Middleware\TokenVerificationMiddleware;

//user login
Route::post('/login', [AuthController::class, 'login'])->name('login');


//user logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([TokenVerificationMiddleware::class])->group(function (){

/**
 * =====================
 * Role Management Routes
 * =====================
 */
Route::post('/create-role', [RoleController::class, 'createRole'])->middleware('permission:create-role')->name('role.create');
Route::post('/update-role', [RoleController::class, 'updateRole'])->middleware('permission:update-role')->name('role.update');
Route::get('/delete-role', [RoleController::class, 'deleteRole'])->middleware('permission:delete-role')->name('role.delete');


/**
 * =====================
 * User Management Routes
 * =====================
 */
Route::post('/create-user', [UserController::class, 'createUser'])->middleware('permission:create-user')->name('user.create');
Route::post('/update-user', [UserController::class, 'updateUser'])->middleware('permission:update-user')->name('user.update');
Route::get('/delete-user', [UserController::class, 'deleteUser'])->middleware('permission:delete-user')->name('user.delete');


/**
 * =====================
 * Product Management Routes
 * =====================
 */
Route::post('/create-product', [ProductController::class, 'createProduct'])->middleware('permission:create-product')->name('product.create');
Route::post('/update-product', [ProductController::class, 'updateProduct'])->middleware('permission:update-product')->name('product.update');
Route::get('/delete-product', [ProductController::class, 'deleteProduct'])->middleware('permission:delete-product')->name('product.delete');


/**
 * =====================
 * Issue Product Routes
 * =====================
 */

Route::post('/issue-product', [ProductIssueController::class, 'issueProduct'])->middleware('permission:issue-product')->name('product.issue');


/**
 * =====================
 * Category Routes
 * =====================
 */

Route::post('/create-category', [CategoryController::class, 'createCategory'])->middleware('permission:create-category')->name('category.create');
Route::post('/update-category', [CategoryController::class, 'updateCategory'])->middleware('permission:update-category')->name('category.update');
Route::get('/delete-category', [CategoryController::class, 'deleteCategory'])->middleware('permission:delete-category')->name('category.delete');


/**
 * =====================
 * Requisition Routes
 * =====================
 */
Route::post('/create-requisition', [RequisitionController::class, 'createRequisition'])->middleware('permission:create-requisition')->name('requisition.create');
Route::get('/delete-requisition', [RequisitionController::class, 'deleteRequisition'])->middleware('permission:delete-requisition')->name('requisition.delete');


// Requisition Approval & Received

Route::get('/requisition-approve-request', [RequisitionApproveReceiveController::class, 'requisitionApproveRequest'])->middleware('permission:approve-reuisition-receive')->name('requisition.approve.request');
Route::post('/update-requisition-request', [RequisitionApproveReceiveController::class, 'updateRequisitionRequest'])->middleware('permission:approve-reuisition-receive')->name('requisition.update.request');
Route::post('/requisition-received-request', [RequisitionApproveReceiveController::class, 'requisitionReceivedRequest'])->middleware('permission:receive-requisition')->name('requisition.received.request');


/**
 * =====================
 * Floor Receive Routes
 * =====================
 */

Route::get('/approve-floor-recieve', [FloorReceiveController::class, 'approveFloorRecieve'])->middleware('permission:approve-floor-receive')->name('product.approve.floor.recieve');
Route::post('/update-floor-recieve', [FloorReceiveController::class, 'updateFloorReceive'])->middleware('permission:approve-floor-receive')->name('product.update.floor.receive');
Route::post('/floor-receive', [FloorReceiveController::class, 'floorReceive'])->middleware('permission:receive-floor-receive')->name('product.floor.receive');


/**
 * =====================
 * Purchase Routes
 * =====================
 */

Route::post('/create-purchase', [PurchaseController::class, 'createPurchase'])->middleware('permission:create-purchase')->name('purchase.create');
Route::post('/update-purchase', [PurchaseController::class, 'updatePurchase'])->middleware('permission:update-purchase')->name('purchase.update');
Route::get('/delete-purchase', [PurchaseController::class, 'deletePurchase'])->middleware('permission:delete-purchase')->name('purchase.delete');


/**
 * =====================
 * Vendor Routes
 * =====================
 */

Route::post('/create-vendor', [VendorController::class, 'createVendor'])->middleware('permission:create-vendor')->name('vendor.create');
Route::post('/update-vendor', [VendorController::class, 'updateVendor'])->middleware('permission:update-vendor')->name('vendor.update');
Route::get('/delete-vendor', [VendorController::class, 'deleteVendor'])->middleware('permission:delete-vendor')->name('vendor.delete');



});



