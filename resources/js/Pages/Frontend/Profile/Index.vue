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
                        <img :src="profileForm.avatar ? '/' + profileForm.avatar : '/images/img/User-avatar.png'" alt="Avatar" class="shopee-clone-avatar">
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
                                    <form class="shopee-clone-profile-form" @submit.prevent="updateProfile">
                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Username</label>
                                            <div class="shopee-clone-form-value">{{ profileForm.username }} </div>
<!--                                            <div class="shopee-clone-form-value">{{ profileForm.dob.month }} </div>-->
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Name</label>
                                            <input type="text" class="form-control shopee-clone-form-input" v-model="profileForm.full_name">
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Email</label>
                                            <div class="shopee-clone-form-value-with-action">
                                                <span>{{ showFullEmail ? auth?.user?.email : maskEmail(auth?.user?.email) }}</span>
                                                <a href="#" class="shopee-clone-change-link" @click.prevent="toggleEmailVisibility">
                                                    <i :class="showFullEmail ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Phone Number</label>
                                            <div class="shopee-clone-form-value-with-action" v-if="!isEditingPhone">
                                                <span>{{ maskPhone(auth?.user?.phone) }}</span>
                                                <a href="#" class="shopee-clone-change-link" @click.prevent="startEditPhone">Change</a>
                                            </div>
                                            <div v-else class="d-flex align-items-center gap-2">
                                                <input
                                                    type="text"
                                                    class="form-control shopee-clone-form-input flex-grow-1"
                                                    v-model="phoneForm.phone"
                                                    placeholder="Enter new phone number"
                                                >
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary"
                                                    @click="savePhone"
                                                    :disabled="isLoadingPhone"
                                                >
                                                    <span v-if="isLoadingPhone" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                    Save
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    @click="cancelEditPhone"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>

                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label">Gender</label>
                                            <div class="shopee-clone-gender-options">
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input shopee-clone-radio"
                                                        type="radio"
                                                        name="gender"
                                                        id="male"
                                                        value="male"
                                                        v-model="profileForm.gender"
                                                        :disabled="isGenderDisabled"
                                                    >
                                                    <label class="form-check-label shopee-clone-radio-label" for="male">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input shopee-clone-radio"
                                                        type="radio"
                                                        name="gender"
                                                        id="female"
                                                        value="female"
                                                        v-model="profileForm.gender"
                                                        :disabled="isGenderDisabled"
                                                    >
                                                    <label class="form-check-label shopee-clone-radio-label" for="female">Female</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input shopee-clone-radio"
                                                        type="radio"
                                                        name="gender"
                                                        id="other"
                                                        value="other"
                                                        v-model="profileForm.gender"
                                                        :disabled="isGenderDisabled"
                                                    >
                                                    <label class="form-check-label shopee-clone-radio-label" for="other">Other</label>
                                                </div>
                                            </div>
                                            <small v-if="isGenderDisabled" class="text-muted">Gender cannot be changed once set</small>
                                        </div>

                                        <div class="shopee-clone-form-group mb-3">
                                            <label class="shopee-clone-form-label">Date of Birth</label>
                                            <div class="row g-2 w-75">
                                                <!-- DAY -->
                                                <div class="col-4">
                                                    <select
                                                        class="form-select shopee-clone-form-select"
                                                        v-model="profileForm.dob.day"
                                                        :disabled="isDobDisabled"
                                                    >
                                                        <option value="">Day</option>
                                                        <option v-for="day in daysInMonth" :key="day" :value="day">
                                                            {{ day }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- MONTH -->
                                                <div class="col-4">
                                                    <select
                                                        class="form-select shopee-clone-form-select"
                                                        v-model="profileForm.dob.month"
                                                        :disabled="isDobDisabled"
                                                    >
                                                        <option value="">Month</option>
                                                        <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                                            {{ month }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- YEAR -->
                                                <div class="col-4">
                                                    <select
                                                        class="form-select shopee-clone-form-select"
                                                        v-model="profileForm.dob.year"
                                                        :disabled="isDobDisabled"
                                                    >
                                                        <option value="">Year</option>
                                                        <option v-for="year in years" :key="year" :value="year">
                                                            {{ year }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="isDobDisabled" class="text-muted mb-4 text-center">Date of birth cannot be changed once set</div>
                                        <div class="shopee-clone-form-group">
                                            <label class="shopee-clone-form-label"></label>
                                            <button type="submit" class="btn shopee-clone-save-btn" :disabled="isLoading">
                                                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <div class="shopee-clone-avatar-section">
                                        <div class="shopee-clone-avatar-preview">
                                            <img :src="previewImage || (profileForm.avatar ? '/' + profileForm.avatar : '/images/img/User-avatar.png')" alt="Avatar" class="shopee-clone-avatar-large" />
                                        </div>
<!--                                        <input class="btn shopee-clone-upload-btn" type="file" value="Select Image">-->
                                        <div class="file-input-wrapper">
                                            <label class="file-input-label">
                                                Chọn Ảnh
                                                <input type="file" id="fileInput" accept="image/jpeg,image/png" @change="onFileSelected">
                                            </label>
                                        </div>
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
                            <div class="shopee-clone-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="shopee-clone-card-title">Notifications</h5>
                                    <p class="shopee-clone-card-subtitle">Manage your notifications</p>
                                </div>
                                <div v-if="hasUnreadNotifications">
                                    <button
                                        class="btn btn-primary btn-sm"
                                        @click="markAllAsRead"
                                        :disabled="markingAllAsRead"
                                    >
                                        <i class="bi bi-check2-all"></i>
                                        {{ markingAllAsRead ? 'Marking...' : 'Mark All as Read' }}
                                    </button>
                                </div>
                            </div>

                            <div
                                v-for="notification in userNotifications"
                                :key="notification.id"
                                class="shopee-clone-notification-item rounded"
                                :class="{ 'bg-body-secondary bg-opacity-10': !notification.is_read }"
                            >
                                <div class="d-flex align-items-start">
                                    <div class="position-relative me-3">
                                        <i
                                            :class="notification.icon"
                                            class="text-warning"
                                            style="font-size: 24px;"
                                        ></i>
                                        <!-- Unread indicator dot -->
                                        <span
                                            v-if="!notification.is_read"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="font-size: 8px; width: 8px; height: 8px;"
                                        >
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" :class="{ 'fw-bold': !notification.is_read }">
                                            {{ notification.title }}
                                        </h6>
                                        <p class="text-muted mb-1" style="font-size: 14px;">
                                            {{ notification.message }}
                                        </p>
                                        <small class="text-muted">{{ notification.created_at }}</small>
                                    </div>
                                    <div>
                                        <button
                                            v-if="!notification.is_read"
                                            class="btn btn-outline-primary btn-sm"
                                            @click="markAsRead(notification.id)"
                                            :disabled="markingAsRead[notification.id]"
                                        >
                                            <i class="bi bi-check2-circle"></i>
                                            {{ markingAsRead[notification.id] ? 'Marking...' : 'Mark as read' }}
                                        </button>
                                        <span v-else class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Read
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty state -->
                            <div v-if="userNotifications.length === 0" class="text-center py-5">
                                <i class="bi bi-bell-slash text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mt-3">No notifications yet</p>
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

                            <div class="shopee-clone-order-item rounded" v-for="order in userOrders" :key="order.id">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                      <span class="badge fs-6"
                                            :class="order.status === 'delivered' ? 'bg-success' : 'bg-warning text-dark'">
                                        {{ capitalize(order.status) }}
                                      </span>
                                        <span class="ms-2 text-muted" style="font-size: 14px;">Order ID: #{{ order.id }}</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 14px;">{{ formatDate(order.created_at) }}</span>
                                </div>
                                <hr>
                                <!-- Danh sách item trong order -->
                                <div v-for="(item, idx) in order.items" :key="idx" class=" mb-2 pt-2 pb-3 border-bottom">
                                    <Link :href="route('detail.index', item.product_id)" class="d-flex w-100">
                                        <img :src="item.image || '/placeholder.svg?height=80&width=80'" alt="Product"
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-1 fs-5">{{ item.product_name }}</h6>
                                            <p class="text-muted mb-1" style="font-size: 14px;">Variant: {{ item.variant_size || '-' }}</p>
                                            <p class="text-muted mb-0" style="font-size: 14px;">x{{ item.quantity }}</p>
                                        </div>
                                        <div class="text-end d-flex align-items-center">
                                            <p class="mb-0 text-danger fw-bold fs-6">₫{{ formatCurrency(item.price * 1 * item.quantity) }}</p>
                                        </div>
                                    </Link>
                                </div>

                                <div class="d-flex justify-content-end my-4 fs-5">
                                        Total: <span class="text-danger fw-bold fs-5 ms-5">₫{{ formatCurrency(order.total) }}</span>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm me-2 fs-5 fw-bold px-3 py-2" @click="reorder(order)">Buy Again</button>
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

                            <button class="btn shopee-clone-save-btn mb-4 rounded" @click="openAddressDialog">
                                <i class="bi bi-plus-lg me-2"></i>Add New Address
                            </button>

                            <!-- Address List -->
                            <div class="border rounded p-3 mb-3" v-for="address in userAddresses" :key="address.id">
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
                            <div v-if="userAddresses.length === 0" class="text-center py-5">
                                <i class="bi bi-geo-alt" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">No addresses added yet</p>
                            </div>
                        </div>

                        <!-- Address Dialog Modal -->
                        <div class="modal fade" :class="{ show: showAddressDialog }" :style="{ display: showAddressDialog ? 'block' : 'none' }" tabindex="-1" v-if="showAddressDialog">
                            <div class="modal-dialog modal-md">
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
                                                        {{ defaultAddress !== '' && locationDisplayText === '' && isEditMode ? defaultAddress : locationDisplayText !== '' ? locationDisplayText : 'City, District, Ward' }}
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
                                                <button type="button" class="btn text-white" style="background-color: #ee4d2d;" @click="saveAddress">
                                                    <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                    Submit
                                                </button>
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

                            <form class="shopee-clone-profile-form" @submit.prevent="changePassword">
                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">Current Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Enter current password" v-model="passwordForm.current_password">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">New Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Enter new password" v-model="passwordForm.new_password">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label">Confirm Password</label>
                                    <input type="password" class="form-control shopee-clone-form-input" placeholder="Re-enter new password" v-model="passwordForm.new_password_confirmation">
                                </div>

                                <div class="shopee-clone-form-group">
                                    <label class="shopee-clone-form-label"></label>
                                    <button type="submit" class="btn shopee-clone-save-btn" :disabled="isLoading">
                                        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Confirm
                                    </button>
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

<script lang="ts">
import {Head, Link} from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import axios from "axios";
import Swal from 'sweetalert2';
axios.defaults.withCredentials = true;

export default {
    components: {
        Menu,
        Head,
        Link,
    },
    props: {
        auth: Object,
        csrf_token: String,
        notifications: Array,
        orders: Array,
        addresses: Array,
    },
    data() {
        return {
            activeTab: 'profile',
            showAddressDialog: false,
            isEditMode: false,
            isLoading: false,
            selectedLocation: '',
            defaultAddress: '',
            showLocationDropdown: false,
            currentLocationLevel: 'city',
            selectedCity: null as any,
            selectedDistrict: null as any,
            selectedWard: null as any,
            // Dữ liệu location sẽ được load từ API
            locationData: {
                cities: [] as any[],
                districts: [] as any[],
                wards: [] as any[],
            },
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],

            currentYear: new Date().getFullYear(),
            userAddresses: [] as any[],
            userNotifications: [] as any[],
            userOrders: [] as any[],
            addressForm: {
                id: null,
                name: '',
                phone: '',
                address: '',
                ward_id: null,
                district_id: null,
                province_id: null,
                is_default: false,
            },
            profileForm: {
                username: '',
                full_name: '',
                phone: '',
                gender: '',
                avatar: '',
                dob: {
                    year: null as number | null,
                    month: null as number | null,
                    day: null as number | null,
                } as any,
            },
            passwordForm: {
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            },
            previewImage: null as string | null,
            selectedFile: null as File | null,
            showFullEmail: false,
            isEditingPhone: false,
            isGenderDisabled: false,
            isDobDisabled: false,
            isLoadingPhone: false,
            phoneForm: {
                phone: '',
            },
            markingAllAsRead: false,
            markingAsRead: {} as any,
        };
    },

    computed: {
        currentLocationList() {
            if (this.currentLocationLevel === 'city') {
                return this.locationData.cities;
            } else if (this.currentLocationLevel === 'district' && this.selectedCity) {
                return this.locationData.districts;
            } else if (this.currentLocationLevel === 'ward' && this.selectedDistrict) {
                return this.locationData.wards;
            }
            return [];
        },
        years() {
            // 80 năm trở lại từ hiện tại
            return Array.from({ length: 80 }, (_, i) => this.currentYear - i)
        },
        daysInMonth() {
            const month = Number(this.profileForm.dob.month)
            const year = Number(this.profileForm.dob.year)

            if (!month || !year) {
                return Array.from({ length: 31 }, (_, i) => i + 1)
            }

            // Lấy số ngày của tháng (auto xét năm nhuận)
            const days = new Date(year, month, 0).getDate()
            return Array.from({ length: days }, (_, i) => i + 1)
        },
        locationDisplayText() {
            const parts = [];
            if (this.selectedCity) parts.push(this.selectedCity.name);
            if (this.selectedDistrict) parts.push(this.selectedDistrict.name);
            if (this.selectedWard) parts.push(this.selectedWard.name);
            return parts.join(', ');
        },
        hasUnreadNotifications() {
            return this.userNotifications.some(notification => !notification.is_read);
        },
    },
    watch: {
        // Khi đổi tháng hoặc năm, nếu ngày không hợp lệ thì reset lại
        'profileForm.dob.month'() {
            this.adjustDay()
        },
        'profileForm.dob.year'() {
            this.adjustDay()
        }
    },
    mounted() {
        // Initialize data from props
        this.userAddresses = this.addresses || [];
        this.userNotifications = this.notifications || [];
        this.userOrders = this.orders || [];

        this.addressForm.name = this.userAddresses[0].name;
        this.addressForm.phone = this.userAddresses[0].phone;
        this.addressForm.address = this.userAddresses[0].address;
        console.log('userAdded', this.userAddresses);
        if (this.userAddresses && this.userAddresses[0].province.length > 0 && this.userAddresses[0].district.length > 0 && this.userAddresses[0].ward.length > 0) {
            this.defaultAddress = this.userAddresses[0].province + ', ' + this.userAddresses[0].district + ', ' + this.userAddresses[0].ward;
        }
        // Initialize profile form
        if (this.auth?.user) {
            console.log('Auth User:', this.profileForm);
            this.profileForm.username = this.auth.user.username || '';
            this.profileForm.full_name = this.auth.user.full_name || '';
            this.profileForm.phone = this.auth.user.phone || '';
            this.profileForm.avatar = this.auth.user.avatar
                ? 'storage/' + this.auth.user.avatar
                : '';
            this.profileForm.gender = this.auth.user.gender || '';

            // Initialize dob as object first
            this.profileForm.dob = {
                year: null,
                month: null,
                day: null
            };

            // Then populate if user has dob
            if (this.auth?.user?.dob) {
                const [year, month, day] = this.auth.user.dob.split('-');
                this.profileForm.dob = {
                    year: Number(year),
                    month: Number(month),
                    day: Number(day)
                };
            }

            // Disable gender and dob fields if already set
            this.isGenderDisabled = !!this.auth.user.gender;
            this.isDobDisabled = !!this.auth.user.dob;
        }

        // Load location data
        this.loadLocationData();
    },

    methods: {
        capitalize(s){ if(!s) return ''; return s.charAt(0).toUpperCase()+s.slice(1); },
        formatDate(raw){
            if (typeof raw === 'string' && raw.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                const [d,m,y]=raw.split('/'); return new Date(`${y}-${m}-${d}`).toLocaleDateString();
            }
            const d=new Date(raw); return isNaN(d)?raw:d.toLocaleDateString();
        },
        formatCurrency(value){
            const n = Number(value) || 0;
            return n.toLocaleString('vi-VN', { maximumFractionDigits: 2 });
        },
        reorder(row) { console.log('Reorder row', row.rowId); },

        setActiveTab(tab) {
            this.activeTab = tab;
        },

        // Helper methods
        adjustDay() {
            const maxDays = this.daysInMonth.length
            if (this.profileForm.dob.day > maxDays) {
                this.profileForm.dob.day = maxDays
            }
        },
        getDobString() {
            const { year, month, day } = this.profileForm.dob
            if (year && month && day) {
                // Format chuẩn YYYY-MM-DD
                return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`
            }
            return ''
        },
        maskEmail(email) {
            if (!email) return 'No email';
            const [name, domain] = email.split('@');
            if (name.length <= 2) return email;
            const maskedName = name.charAt(0) + '*'.repeat(name.length - 2) + name.charAt(name.length - 1);
            return `${maskedName}@${domain}`;
        },

        maskPhone(phone) {
            if (!phone) return 'No phone';
            if (phone.length <= 4) return phone;
            const visiblePart = phone.slice(-3);
            const maskedPart = '*'.repeat(phone.length - 3);
            return maskedPart + visiblePart;
        },

        // Email visibility methods
        toggleEmailVisibility() {
            this.showFullEmail = !this.showFullEmail;
        },

        // Phone editing methods
        startEditPhone() {
            this.isEditingPhone = true;
            this.phoneForm.phone = this.auth?.user?.phone || '';
        },

        cancelEditPhone() {
            this.isEditingPhone = false;
            this.phoneForm.phone = '';
        },

        async savePhone() {
            if (!this.phoneForm.phone.trim()) {
                Swal.fire('Error', 'Please enter a phone number', 'error');
                return;
            }

            this.isLoadingPhone = true;
            try {
                const response = await axios.post('/api/profile/update-phone', {
                    phone: this.phoneForm.phone
                });

                if (response.data.success) {
                    // Cập nhật phone number trong auth object
                    if (this.auth?.user) {
                        this.auth.user.phone = this.phoneForm.phone;
                    }

                    this.isEditingPhone = false;
                    this.phoneForm.phone = '';

                    Swal.fire('Success', 'Phone number updated successfully!', 'success');
                }
            } catch (error: any) {
                console.error('Error updating phone:', error);
                if (error.response?.data?.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Failed to update phone number', 'error');
                }
            } finally {
                this.isLoadingPhone = false;
            }
        },

        // Profile methods
        async updateProfile() {
            this.isLoading = true;
            try {
                // Tạo FormData để gửi file và data
                const formData = new FormData();

                // Thêm thông tin profile
                formData.append('full_name', this.profileForm.full_name || '');
                formData.append('gender', this.profileForm.gender || '');

                // Thêm ngày sinh nếu có đầy đủ thông tin
                if (this.profileForm.dob && this.profileForm.dob.year && this.profileForm.dob.month && this.profileForm.dob.day) {
                    const dobString = this.getDobString();
                    formData.append('dob', dobString);
                }

                // Thêm file avatar nếu có chọn file mới
                if (this.selectedFile) {
                    formData.append('avatar', this.selectedFile);
                }

                // Thêm CSRF token nếu có
                if (this.csrf_token) {
                    formData.append('_token', this.csrf_token);
                }

                // Gửi request với FormData
                const response = await axios.post('/api/profile/update', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (response.data.success) {
                    // Cập nhật avatar URL nếu có
                    if (response.data.user && response.data.user.avatar) {
                        this.profileForm.avatar = response.data.user.avatar;
                    }

                    // Reset preview image và selected file
                    this.previewImage = null;
                    this.selectedFile = null;

                    Swal.fire('Success', 'Profile updated successfully!', 'success');
                }
            } catch (error: any) {
                console.error('Error updating profile:', error);

                // Log chi tiết lỗi để debug
                if (error.response) {
                    console.error('Error response:', error.response.data);
                    console.error('Error status:', error.response.status);
                    console.error('Error headers:', error.response.headers);
                }

                if (error.response?.data?.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else if (error.response?.status === 405) {
                    Swal.fire('Error', 'Method not allowed. Please refresh the page and try again.', 'error');
                } else {
                    Swal.fire('Error', 'Failed to update profile', 'error');
                }
            } finally {
                this.isLoading = false;
            }
        },

        async changePassword() {
            this.isLoading = true;
            try {
                const response = await axios.post('/api/profile/change-password', this.passwordForm);
                if (response.data.success) {
                    Swal.fire('Success', 'Password changed successfully!', 'success');
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                    };
                }
            } catch (error) {
                console.error('Error changing password:', error);
                if (error.response?.data?.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Failed to change password', 'error');
                }
            } finally {
                this.isLoading = false;
            }
        },

        // Address methods
        openAddressDialog() {
            this.showAddressDialog = true;
            this.isEditMode = false;
            this.resetAddressForm();
        },

        editAddress(address) {
            this.showAddressDialog = true;
            this.isEditMode = true;
            this.addressForm = { ...address };
        },

        closeAddressDialog() {
            this.showAddressDialog = false;
            this.resetAddressForm();
            this.showLocationDropdown = false;
        },

        resetAddressForm() {
            this.addressForm = {
                id: null,
                name: '',
                phone: '',
                address: '',
                ward_id: null,
                district_id: null,
                province_id: null,
                is_default: false,
            };
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
        },

        async saveAddress() {
            if (!this.validateAddressForm()) return;

            this.isLoading = true;
            try {
                const url = this.isEditMode ? `/api/profile/addresses/${this.addressForm.id}` : '/api/profile/addresses';
                const method = this.isEditMode ? 'put' : 'post';

                const response = await axios[method](url, this.addressForm);

                if (response.data.success) {
                    Swal.fire('Success', response.data.message, 'success');
                    this.closeAddressDialog();
                    // Refresh addresses
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error saving address:', error);
                Swal.fire('Error', 'Failed to save address', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async deleteAddress(addressId) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    const response = await axios.delete(`/api/profile/addresses/${addressId}`);
                    if (response.data.success) {
                        Swal.fire('Deleted!', 'Address has been deleted.', 'success');
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error deleting address:', error);
                    Swal.fire('Error', 'Failed to delete address', 'error');
                }
            }
        },

        validateAddressForm() {
            if (!this.addressForm.name || !this.addressForm.phone || !this.addressForm.address) {
                Swal.fire('Error', 'Please fill in all required fields', 'error');
                return false;
            }
            if (!this.selectedWard || !this.selectedDistrict || !this.selectedCity) {
                Swal.fire('Error', 'Please select city, district, and ward', 'error');
                return false;
            }

            this.addressForm.ward_id = this.selectedWard.id;
            this.addressForm.district_id = this.selectedDistrict.id;
            this.addressForm.province_id = this.selectedCity.id;

            return true;
        },

        // Location methods
        toggleLocationDropdown() {
            this.showLocationDropdown = !this.showLocationDropdown;
        },

        setLocationLevel(level) {
            this.currentLocationLevel = level;
        },

        async selectCity(city) {
            this.selectedCity = city;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'district';

            // Load districts for selected city
            await this.loadDistricts(city.id);
        },

        async selectDistrict(district) {
            this.selectedDistrict = district;
            this.selectedWard = null;
            this.currentLocationLevel = 'ward';

            // Load wards for selected district
            await this.loadWards(district.id);
        },

        selectWard(ward) {
            this.selectedWard = ward;
            this.showLocationDropdown = false;
        },

        clearLocation() {
            this.selectedCity = null;
            this.selectedDistrict = null;
            this.selectedWard = null;
            this.currentLocationLevel = 'city';
            // Reset districts and wards
            this.locationData.districts = [];
            this.locationData.wards = [];
        },

        // Notification methods
        async markAsRead(notificationId) {
            try {
                await axios.post(`/api/profile/notifications/${notificationId}/read`);
                const notification = this.userNotifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.is_read = true;
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
            this.markingAllAsRead = true;
            try {
                const response = await axios.post('/api/profile/notifications/mark-all-as-read');
                if (response.data.success) {
                    this.userNotifications.forEach(notification => {
                        notification.is_read = true;
                    });
                    Swal.fire('Success', 'All notifications marked as read!', 'success');
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
                Swal.fire('Error', 'Failed to mark notifications as read', 'error');
            } finally {
                this.markingAllAsRead = false;
            }
        },

        // Order methods
        reorder(order) {
            // Logic to reorder items
            console.log('Reordering:', order);
        },

        // Avatar preview method
        onFileSelected(event) {
            const file = event.target.files[0];
            if (file) {
                // Kiểm tra kích thước file (1MB = 1024*1024 bytes)
                if (file.size > 1024 * 1024) {
                    alert('File size must be less than 1MB');
                    return;
                }

                // Kiểm tra định dạng file
                const allowedTypes = ['image/jpeg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPEG and PNG formats are allowed');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);

                // Lưu file đã chọn để gửi lên server
                this.selectedFile = file;
            }
        },

        // Location data methods
        async loadLocationData() {
            try {
                // Chỉ load provinces khi component mount
                const response = await axios.get('/api/locations/provinces');
                if (response.data.success) {
                    this.locationData.cities = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        },

        async loadDistricts(provinceId) {
            try {
                const response = await axios.get(`/api/locations/districts/${provinceId}`);
                if (response.data.success) {
                    this.locationData.districts = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading districts:', error);
            }
        },

        async loadWards(districtId) {
            try {
                const response = await axios.get(`/api/locations/wards/${districtId}`);
                if (response.data.success) {
                    this.locationData.wards = response.data.data || [];
                }
            } catch (error) {
                console.error('Error loading wards:', error);
            }
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
