<template>
    <Head title="My Account"/>
    <Menu></Menu>

    <div>
        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">My Account</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active text-white">My Account</li>
            </ol>
        </div>
        <!-- Single Page Header End -->


        <!-- Contact Start -->
        <div class="container-fluid contact py-5">
            <div class="container py-5 row m-auto">
                <div class="col-3">
                    <div class="shopee-clone-user-info">
                        <img src="/images/img/User-avatar.png" alt="Avatar" class="shopee-clone-avatar">
                        <div class="shopee-clone-user-details">
                            <h6 class="shopee-clone-username">nguyenvana</h6>
                            <a href="#" class="shopee-clone-edit-profile">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                    <nav class="shopee-clone-nav">
                        <div class="shopee-clone-nav-section">
                            <ul class="shopee-clone-nav-list">
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'profile' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('profile')"><i class="bi bi-person shopee-clone-nav-icon me-2"></i>Profile</a>
                                </li>
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'notifications' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('notifications')"><i class="bi bi-bell shopee-clone-nav-icon me-2"></i>Notifications</a>
                                </li>
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'orders' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('orders')"><i class="bi bi-bag shopee-clone-nav-icon me-2"></i>My Orders</a>
                                </li>
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'address' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('address')"><i class="bi bi-geo-alt shopee-clone-nav-icon me-2"></i>Address</a>
                                </li>
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'password' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('password')"><i class="bi bi-key shopee-clone-nav-icon me-2"></i>Change Password</a>
                                </li>
                                <li class="shopee-clone-nav-item" :class="{ active: activeTab === 'privacy' }">
                                    <a class="shopee-clone-nav-link" @click.prevent="setActiveTab('privacy')"><i class="bi bi-shield-lock shopee-clone-nav-icon me-2"></i>Privacy Settings</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>



                <div class="col-9 shopee-clone-main-content">
                    <!-- Profile Tab Content -->
                    <div v-show="activeTab === 'profile'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">My Profile</h5>
                                <p class="shopee-clone-card-subtitle">Manage your profile information to secure your account</p>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <form class="shopee-clone-profile-form">
                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Username</label>
                                            <div class="shopee-clone-form-value">nguyenvana</div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Name</label>
                                            <input type="text" class="form-control shopee-clone-form-input" value="Nguyen Van A">
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Email</label>
                                            <div class="shopee-clone-form-value-with-action">
                                                <span>ng***a@gmail.com</span>
                                                <a href="#" class="shopee-clone-change-link">Change</a>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Phone Number</label>
                                            <div class="shopee-clone-form-value-with-action">
                                                <span>*******789</span>
                                                <a href="#" class="shopee-clone-change-link">Change</a>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Gender</label>
                                            <div class="shopee-clone-gender-options">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input shopee-clone-radio" type="radio" name="gender" id="male" value="male">
                                                    <label class="form-check-label shopee-clone-radio-label" for="male">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input shopee-clone-radio" type="radio" name="gender" id="female" value="female" checked>
                                                    <label class="form-check-label shopee-clone-radio-label" for="female">Female</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input shopee-clone-radio" type="radio" name="gender" id="other" value="other">
                                                    <label class="form-check-label shopee-clone-radio-label" for="other">Other</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Date of Birth</label>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <select class="form-select shopee-clone-form-select">
                                                        <option>1</option>
                                                        <option>2</option>
                                                        <option selected>15</option>
                                                        <option>30</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-select shopee-clone-form-select">
                                                        <option>January</option>
                                                        <option>February</option>
                                                        <option selected>May</option>
                                                        <option>December</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-select shopee-clone-form-select">
                                                        <option>1990</option>
                                                        <option selected>1995</option>
                                                        <option>2000</option>
                                                        <option>2005</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label"></label>
                                            <button type="submit" class="btn shopee-clone-save-btn">Save</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <div class="shopee-clone-avatar-section">
                                        <div class="shopee-clone-avatar-preview">
                                            <img src="/images/img/User-avatar.png" alt="Avatar" class="shopee-clone-avatar-large">
                                        </div>
                                        <button class="btn shopee-clone-upload-btn">Select Image</button>
                                        <div class="shopee-clone-avatar-note">
                                            <p>Maximum file size 1 MB</p>
                                            <p>Format: .JPEG, .PNG</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab Content -->
                    <div v-show="activeTab === 'notifications'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">Notifications</h5>
                                <p class="shopee-clone-card-subtitle">Manage your notifications</p>
                            </div>

                            <div class="shopee-clone-notification-item">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-bell-fill text-warning me-3" style="font-size: 24px;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Order delivered successfully</h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">Order #123456 has been delivered to you. Please rate the product.</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="shopee-clone-notification-item">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-tag-fill text-danger me-3" style="font-size: 24px;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">New voucher for you</h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">You have a 50,000đ discount voucher for your next order.</p>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                            </div>

                            <div class="shopee-clone-notification-item">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-truck text-primary me-3" style="font-size: 24px;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Order is being shipped</h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">Order #123455 is on its way to you.</p>
                                        <small class="text-muted">3 days ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab Content -->
                    <div v-show="activeTab === 'orders'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">My Orders</h5>
                                <p class="shopee-clone-card-subtitle">Manage your orders</p>
                            </div>

                            <div class="shopee-clone-order-item">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-success">Delivered</span>
                                        <span class="ms-2 text-muted" style="font-size: 14px;">Order ID: #123456</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 14px;">15/03/2024</span>
                                </div>
                                <div class="d-flex">
                                    <img src="/placeholder.svg?height=80&width=80" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1">Premium Men's Cotton T-shirt</h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">Variant: White, Size M</p>
                                        <p class="text-muted mb-0" style="font-size: 14px;">x1</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 text-danger fw-bold">₫299.000</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm me-2">Buy Again</button>
                                    <button class="btn btn-outline-secondary btn-sm">Contact Seller</button>
                                </div>
                            </div>

                            <div class="shopee-clone-order-item">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-warning text-dark">Shipping</span>
                                        <span class="ms-2 text-muted" style="font-size: 14px;">Order ID: #123455</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 14px;">18/03/2024</span>
                                </div>
                                <div class="d-flex">
                                    <img src="/placeholder.svg?height=80&width=80" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1">Unisex Sport Shoes</h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">Variant: Black, Size 42</p>
                                        <p class="text-muted mb-0" style="font-size: 14px;">x1</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 text-danger fw-bold">₫450.000</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm">Contact Seller</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Tab Content -->
                    <div v-show="activeTab === 'address'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">My Addresses</h5>
                                <p class="shopee-clone-card-subtitle">Manage your shipping addresses</p>
                            </div>

                            <button class="btn shopee-clone-save-btn mb-4" @click="showAddressDialog = true">
                                <i class="bi bi-plus-lg me-2"></i>Add New Address
                            </button>

                            <!-- Address List -->
                            <div class="border rounded p-3 mb-3" v-for="address in addresses" :key="address.id">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="mb-2">
                                            <strong>{{ address.name }}</strong>
                                            <span class="ms-2 text-muted">|</span>
                                            <span class="ms-2 text-muted">{{ address.phone }}</span>
                                        </div>
                                        <p class="text-muted mb-2" style="font-size: 14px;">{{ address.address }}</p>
                                        <p class="text-muted mb-2" style="font-size: 14px;">{{ address.city }}, {{ address.country }}</p>
                                        <span class="badge bg-danger" v-if="address.is_default">Default</span>
                                    </div>
                                    <div>
                                        <a href="#" class="shopee-clone-change-link me-3" @click.prevent="editAddress(address)">Update</a>
                                        <a href="#" class="text-muted" @click.prevent="deleteAddress(address.id)">Delete</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-if="addresses.length === 0" class="text-center py-5">
                                <i class="bi bi-geo-alt" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">No addresses added yet</p>
                            </div>
                        </div>

                        <!-- Address Dialog Modal -->
                        <div class="modal fade" :class="{ show: showAddressDialog }" :style="{ display: showAddressDialog ? 'block' : 'none' }" tabindex="-1" v-if="showAddressDialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ isEditMode ? 'Update Address' : 'New Address' }}</h5>
                                        <button type="button" class="btn-close" @click="closeAddressDialog"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form id="addressForm">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="Full Name" v-model="addressForm.name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="tel" class="form-control" placeholder="Phone Number" v-model="addressForm.phone" required>
                                                </div>
                                            </div>

                                            <div class="position-relative mb-3">
                                                <div class="border rounded p-2 d-flex justify-content-between align-items-center" style="cursor: pointer; min-height: 38px;" @click="toggleLocationDropdown">
                                                    <span :class="{ 'text-muted': !locationDisplayText }">
                                                        {{ locationDisplayText || 'City, District, Ward' }}
                                                    </span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm p-0" @click.stop="clearLocation" v-if="locationDisplayText" style="border: none; background: none;">
                                                            <i class="bi bi-x-circle-fill text-muted"></i>
                                                        </button>
                                                        <i class="bi bi-chevron-down text-muted"></i>
                                                    </div>
                                                </div>

                                                <div v-if="showLocationDropdown" class="border rounded mt-1 bg-white position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow: hidden;">
                                                    <div class="d-flex border-bottom">
                                                        <div
                                                            class="flex-fill text-center py-2"
                                                            :class="{ 'text-danger border-bottom border-danger border-2': currentLocationLevel === 'city' }"
                                                            style="cursor: pointer;"
                                                            @click="setLocationLevel('city')"
                                                        >
                                                            City
                                                        </div>
                                                        <div
                                                            class="flex-fill text-center py-2"
                                                            :class="{ 'text-danger border-bottom border-danger border-2': currentLocationLevel === 'district' }"
                                                            style="cursor: pointer;"
                                                            @click="setLocationLevel('district')"
                                                        >
                                                            District
                                                        </div>
                                                        <div
                                                            class="flex-fill text-center py-2"
                                                            :class="{ 'text-danger border-bottom border-danger border-2': currentLocationLevel === 'ward' }"
                                                            style="cursor: pointer;"
                                                            @click="setLocationLevel('ward')"
                                                        >
                                                            Ward
                                                        </div>
                                                    </div>
                                                    <div style="max-height: 250px; overflow-y: auto;">
                                                        <div
                                                            v-for="item in currentLocationList"
                                                            :key="item.id"
                                                            class="p-2 border-bottom"
                                                            style="cursor: pointer;"
                                                            @click="currentLocationLevel === 'city' ? selectCity(item) : currentLocationLevel === 'district' ? selectDistrict(item) : selectWard(item)"
                                                            @mouseover="$event.target.style.backgroundColor = '#f5f5f5'"
                                                            @mouseout="$event.target.style.backgroundColor = 'white'"
                                                        >
                                                            {{ item.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <textarea class="form-control mb-3" rows="3" placeholder="Street Name, Building, House No" v-model="addressForm.address" required></textarea>

                                            <label class="form-label">Label As:</label>
                                            <div class="d-flex gap-2 mb-3">
                                                <button type="button" class="btn btn-outline-secondary">Home</button>
                                                <button type="button" class="btn btn-outline-secondary">Work</button>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" id="defaultAddress" class="form-check-input" v-model="addressForm.is_default">
                                                <label for="defaultAddress" class="form-check-label">Set as Default Address</label>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" @click="closeAddressDialog">Cancel</button>
                                                <button type="button" class="btn text-white" style="background-color: #ee4d2d;" @click="saveAddress">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal backdrop -->
                        <div class="modal-backdrop fade" :class="{ show: showAddressDialog }" v-if="showAddressDialog"></div>
                    </div>

                    <!-- Change Password Tab Content -->
                    <div v-show="activeTab === 'password'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">Change Password</h5>
                                <p class="shopee-clone-card-subtitle">For security, please do not share your password with others</p>
                            </div>

                            <form class="shopee-clone-profile-form">
                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">Current Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Enter current password">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">New Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Enter new password">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">Confirm Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Re-enter new password">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label"></label>
                                    <button type="submit" class="btn shopee-clone-save-btn">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Privacy Settings Tab Content -->
                    <div v-show="activeTab === 'privacy'" class=" bg-light rounded">
                        <div class="shopee-clone-content-card">
                            <div class="shopee-clone-card-header">
                                <h5 class="shopee-clone-card-title">Privacy Settings</h5>
                                <p class="shopee-clone-card-subtitle">Manage your privacy settings</p>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-1">Recent Activity</h6>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Allow others to see your recent activity</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="recentActivity" checked>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-1">Show Phone Number</h6>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Allow sellers to see your phone number</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showPhone">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-1">Email Notifications</h6>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Receive order and promotion notifications via email</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">SMS Notifications</h6>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Receive order notifications via SMS</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="smsNotif" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <a href="#">
                                <h1 class="text-primary mb-0">Fruitables</h1>
                                <p class="text-secondary mb-0">Fresh products</p>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mx-auto">
                                <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                                <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end pt-3">
                                <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">typesetting, remaining essentially unchanged. It was
                                popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                            <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Condition</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: 1429 Netus Rd, NY 48247</p>
                            <p>Email: Example@gmail.com</p>
                            <p>Phone: +0123 4567 8910</p>
                            <p>Payment Accepted</p>
                            <img src="img/payment.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 my-auto text-center text-md-end text-white">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlist, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->
    </div>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import axios from "axios";
import { useCartStore } from '@/stores/cart';
import  { Link } from '@inertiajs/vue3';
import {useAuthStore} from "@/stores/auth.js";
axios.defaults.withCredentials = true;
export default {
    components: {
        AuthenticatedLayout,
        Menu,
        Head,
        Search,
        Link,
    },
    props: {
        auth: Object,
        csrf_token: String,
    },
    data() {
        return {
            activeTab: 'profile',
            showAddressDialog: false,
            isEditMode: false,
            isLoading: false,
            selectedLocation: '',
            showLocationDropdown: false,
            currentLocationLevel: 'city',
            selectedCity: null,
            selectedDistrict: null,
            selectedWard: null,
            locationData: {
                cities: [
                    { id: 1, name: 'Hồ Chí Minh' },
                    { id: 2, name: 'Hà Nội' },
                    { id: 3, name: 'Bà Rịa - Vũng Tàu' },
                    { id: 4, name: 'Đà Nẵng' },
                ],
                districts: {
                    1: [
                        { id: 11, name: 'Quận 1' },
                        { id: 12, name: 'Quận 2' },
                        { id: 13, name: 'Quận 3' },
                        { id: 14, name: 'Quận Bình Thạnh' },
                    ],
                    2: [
                        { id: 21, name: 'Quận Ba Đình' },
                        { id: 22, name: 'Quận Hoàn Kiếm' },
                        { id: 23, name: 'Quận Đống Đa' },
                    ],
                    3: [
                        { id: 31, name: 'Huyện Long Điền' },
                        { id: 32, name: 'Thành phố Vũng Tàu' },
                        { id: 33, name: 'Thành phố Bà Rịa' },
                    ],
                    4: [
                        { id: 41, name: 'Quận Hải Châu' },
                        { id: 42, name: 'Quận Thanh Khê' },
                        { id: 43, name: 'Quận Sơn Trà' },
                    ],
                },
                wards: {
                    11: [
                        { id: 111, name: 'Phường Bến Nghé' },
                        { id: 112, name: 'Phường Bến Thành' },
                        { id: 113, name: 'Phường Nguyễn Thái Bình' },
                    ],
                    31: [
                        { id: 311, name: 'Thị Trấn Long Hải' },
                        { id: 312, name: 'Thị Trấn Long Điền' },
                        { id: 313, name: 'Xã An Ngãi' },
                        { id: 314, name: 'Xã An Nhứt' },
                        { id: 315, name: 'Xã Phước Hưng' },
                        { id: 316, name: 'Xã Phước Tĩnh' },
                        { id: 317, name: 'Xã Tam Phước' },
                    ],
                },
            },
            addresses: [
                {
                    id: 1,
                    name: 'Nguyen Van A',
                    phone: '0912345789',
                    address: '123 ABC Street',
                    city: 'Ho Chi Minh City',
                    country: 'Vietnam',
                    is_default: true,
                },
                {
                    id: 2,
                    name: 'Nguyen Van B',
                    phone: '0987654321',
                    address: '456 DEF Street',
                    city: 'Hanoi',
                    country: 'Vietnam',
                    is_default: false,
                },
            ],
            addressForm: {
                id: null,
                name: '',
                phone: '',
                address: '',
                city: '',
                postal_code: '',
                country: '',
                is_default: false,
            },
        };
    },
    computed: {
        authStore() {
            return useAuthStore();
        },
        cartStore() {
            return useCartStore();
        },
        flashMessages() {
            return this.$page.props.flash || {};
        },
        locationDisplayText() {
            const parts = [];
            if (this.selectedCity) parts.push(this.selectedCity.name);
            if (this.selectedDistrict) parts.push(this.selectedDistrict.name);
            if (this.selectedWard) parts.push(this.selectedWard.name);
            return parts.join(', ');
        },
        currentLocationList() {
            if (this.currentLocationLevel === 'city') {
                return this.locationData.cities;
            } else if (this.currentLocationLevel === 'district' && this.selectedCity) {
                return this.locationData.districts[this.selectedCity.id] || [];
            } else if (this.currentLocationLevel === 'ward' && this.selectedDistrict) {
                return this.locationData.wards[this.selectedDistrict.id] || [];
            }
            return [];
        },
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        closeAddressDialog() {
            this.showAddressDialog = false;
            this.resetAddressForm();
        },
        resetAddressForm() {
            this.addressForm = {
                id: null,
                name: '',
                phone: '',
                address: '',
                city: '',
                postal_code: '',
                country: '',
                is_default: false,
            };
            this.selectedLocation = '';
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'city';
            this.showLocationDropdown = false;
        },
        editAddress(address) {
            this.addressForm = { ...address };
            // Assuming latitude and longitude are available in address object
            if (address.latitude && address.longitude) {
                this.selectedLocation = `${address.latitude}, ${address.longitude}`;
            }
            // Manually set selected city, district, ward if available from existing address
            // This part might need more sophisticated logic based on your data structure
            if (address.city) {
                const city = this.locationData.cities.find(c => c.name === address.city);
                if (city) {
                    this.selectedCity = city;
                    // Try to find district and ward if they exist in the address
                    if (address.district) {
                        const district = this.locationData.districts[city.id]?.find(d => d.name === address.district);
                        if (district) {
                            this.selectedDistrict = district;
                            if (address.ward) {
                                const ward = this.locationData.wards[district.id]?.find(w => w.name === address.ward);
                                if (ward) {
                                    this.selectedWard = ward;
                                }
                            }
                        }
                    }
                }
            }

            this.isEditMode = true;
            this.showAddressDialog = true;
        },
        deleteAddress(id) {
            // Implement delete address functionality
            console.log('Delete address with id:', id);
            this.addresses = this.addresses.filter(address => address.id !== id);
        },
        saveAddress() {
            this.isLoading = true;
            // Simulate API call
            setTimeout(() => {
                this.isLoading = false;
                // Logic to add or update address
                if (this.isEditMode) {
                    const index = this.addresses.findIndex(addr => addr.id === this.addressForm.id);
                    if (index !== -1) {
                        // Update existing address
                        this.addresses[index] = {
                            ...this.addressForm,
                            city: this.selectedCity ? this.selectedCity.name : this.addressForm.city,
                            district: this.selectedDistrict ? this.selectedDistrict.name : '', // Assuming district and ward are part of address form
                            ward: this.selectedWard ? this.selectedWard.name : '',
                        };
                    }
                } else {
                    // Add new address
                    const newId = this.addresses.length > 0 ? Math.max(...this.addresses.map(a => a.id)) + 1 : 1;
                    this.addresses.push({
                        ...this.addressForm,
                        id: newId,
                        city: this.selectedCity ? this.selectedCity.name : this.addressForm.city,
                        district: this.selectedDistrict ? this.selectedDistrict.name : '',
                        ward: this.selectedWard ? this.selectedWard.name : '',
                    });
                }

                this.showAddressDialog = false;
                this.resetAddressForm();
                alert('Address saved successfully!');
            }, 1000); // Shorter timeout for faster feedback
        },
        selectLocationOnMap() {
            // Implement location selection on map
            this.selectedLocation = '10.762622, 106.660172'; // Example coordinates
        },
        getCurrentLocation() {
            // Implement get current location functionality
            this.selectedLocation = '10.762622, 106.660172'; // Example coordinates
        },
        toggleLocationDropdown() {
            this.showLocationDropdown = !this.showLocationDropdown;
        },
        setLocationLevel(level) {
            this.currentLocationLevel = level;
        },
        selectCity(city) {
            this.selectedCity = city;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'district';
            // Update addressForm.city when a city is selected
            this.addressForm.city = city.name;
        },
        selectDistrict(district) {
            this.selectedDistrict = district;
            this.selectedWard = null;
            this.currentLocationLevel = 'ward';
            // Update addressForm.district when a district is selected
            this.addressForm.district = district.name;
        },
        selectWard(ward) {
            this.selectedWard = ward;
            this.showLocationDropdown = false;
            // Update addressForm.ward when a ward is selected
            this.addressForm.ward = ward.name;
        },
        clearLocation() {
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'city';
            this.showLocationDropdown = false;
            // Clear relevant fields in addressForm
            this.addressForm.city = '';
            this.addressForm.district = '';
            this.addressForm.ward = '';
        },
    },
    setup() {


        return {}
    }
}
</script>

<style lang="scss" scoped>

</style>
