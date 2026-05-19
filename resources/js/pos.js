// // Gắn function vào window để thẻ <body> có thể gọi x-data="posApp()"
// window.posApp = function() {
//     return {
//         // --- 1. TRẠNG THÁI (STATE) ---
//         openFilter: false, 
        
//         // SỬA TẠI ĐÂY: Khôi phục giỏ hàng từ LocalStorage, nếu chưa có thì tạo mảng rỗng []
//         cart: JSON.parse(localStorage.getItem('dyly_pos_cart')) || [],          
        
//         isProcessing: false, 
        
//         medicines: [],  
//         isLoadingMedicines: false, 
//         filters: {
//             search: '', category: 'Tất cả', min_price: '', max_price: '', loai: '', stock: ''
//         },
//         pagination: {
//             current_page: 1, last_page: 1, links: [] 
//         },

//         init() {
//             this.fetchMedicines(); 
//         },

//         // ==========================================
//         // HÀM MỚI: LƯU GIỎ HÀNG XUỐNG TRÌNH DUYỆT
//         // ==========================================
//         saveCart() {
//             localStorage.setItem('dyly_pos_cart', JSON.stringify(this.cart));
//         },

//         // --- 2. GỌI API LẤY DANH SÁCH THUỐC ---
//         async fetchMedicines(page = 1) {
//             this.isLoadingMedicines = true;
//             try {
//                 const params = new URLSearchParams();
//                 if (this.filters.search) params.append('search', this.filters.search);
//                 if (this.filters.category && this.filters.category !== 'Tất cả') params.append('category', this.filters.category);
//                 if (this.filters.min_price) params.append('min_price', this.filters.min_price);
//                 if (this.filters.max_price) params.append('max_price', this.filters.max_price);
//                 if (this.filters.loai) params.append('loai', this.filters.loai);
//                 if (this.filters.stock) params.append('stock', this.filters.stock);
//                 params.append('page', page);

//                 const response = await fetch(`/api/v1/pos/medicines?${params.toString()}`);
//                 const result = await response.json();

//                 if (result.status === 'success') {
//                     this.medicines = result.data.data;
//                     this.pagination = {
//                         current_page: result.data.current_page,
//                         last_page: result.data.last_page,
//                         links: result.data.links
//                     };
//                 }
//             } catch (error) {
//                 console.error("Lỗi khi tải danh sách thuốc:", error);
//             } finally {
//                 this.isLoadingMedicines = false;
//             }
//         },

//         applyFilters() { this.fetchMedicines(1); },
//         setCategory(categoryName) { this.filters.category = categoryName; this.fetchMedicines(1); },
//         clearFilters() { 
//             this.filters = { search: '', category: 'Tất cả', min_price: '', max_price: '', loai: '', stock: '' };
//             this.fetchMedicines(1);
//         },
//         changePage(url) {
//             if (!url) return;
//             const urlObj = new URL(url, window.location.origin);
//             const page = urlObj.searchParams.get('page') || 1;
//             this.fetchMedicines(page);
//         },

//         // --- 3. COMPUTED & TIỆN ÍCH ---
//         get totalAmount() {
//             return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
//         },

//         formatPrice(price) {
//             return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫';
//         },

//         // --- 4. CÁC HÀM XỬ LÝ GIỎ HÀNG ---
//         addToCart(thuoc) {
//             if (thuoc.stock <= 0) {
//                 alert('Sản phẩm này đã hết hàng trong kho!');
//                 return;
//             }

//             let existingItem = this.cart.find(item => item.id === thuoc.id);
//             if (existingItem) {
//                 if (existingItem.quantity < thuoc.stock) {
//                     existingItem.quantity++;
//                 } else {
//                     alert('Số lượng trong giỏ đã đạt giới hạn tồn kho!');
//                 }
//             } else {
//                 this.cart.push({ ...thuoc, quantity: 1 });
//             }
            
//             // LƯU LẠI GIỎ HÀNG KHI THÊM MỚI
//             this.saveCart();
//         },

//         increaseQuantity(id) {
//             let item = this.cart.find(i => i.id === id);
//             if (item && item.quantity < item.stock) {
//                 item.quantity++;
//                 this.saveCart(); // LƯU KHI TĂNG SỐ LƯỢNG
//             }
//         },

//         decreaseQuantity(id) {
//             let item = this.cart.find(i => i.id === id);
//             if (item) {
//                 if (item.quantity > 1) {
//                     item.quantity--;
//                 } else {
//                     this.cart = this.cart.filter(i => i.id !== id);
//                 }
//                 this.saveCart(); // LƯU KHI GIẢM HOẶC XÓA SẢN PHẨM
//             }
//         },

//         clearCart() {
//             if (this.cart.length > 0 && confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
//                 this.cart = [];
//                 this.saveCart(); // LÀM TRỐNG LOCALSTORAGE
//             }
//         },

//         // --- 5. HÀM THANH TOÁN ---
//         async processCheckout() {
//             if (this.cart.length === 0) return;
//             if (!confirm(`Xác nhận thanh toán đơn hàng với tổng tiền: ${this.formatPrice(this.totalAmount)}?`)) return;

//             this.isProcessing = true;
//             try {
//                 const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
//                 const response = await fetch('/api/v1/pos/checkout', {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json',
//                         'Accept': 'application/json',
//                         'X-CSRF-TOKEN': csrfToken 
//                     },
//                     body: JSON.stringify({
//                         cart: this.cart,
//                         total_amount: this.totalAmount,
//                         customer_id: null
//                     })
//                 });

//                 const result = await response.json();

//                 if (response.ok && result.status === 'success') {
//                     // SỬA TẠI ĐÂY: Thay alert bằng confirm để tạo nút chọn In Bill
//                     let inHoaDon = confirm(`✅ Thanh toán thành công! Mã đơn hàng: #HD${result.order_id}\n\nBạn có muốn IN HÓA ĐƠN ngay bây giờ không?`);
                    
//                     if (inHoaDon) {
//                         // result.order_id đang có dạng "0015", cần parse về số nguyên 15
//                         let rawOrderId = parseInt(result.order_id, 10);
                        
//                         // CẬP NHẬT ĐƯỜNG DẪN IN BILL RIÊNG CHO POS
//                         // Mở popup nhỏ gọn thay vì tab lớn
//                         window.open(`/pos/orders/${rawOrderId}/print`, 'PrintBill', 'width=600,height=800');
//                     }
                    
//                     this.cart = []; 
//                     this.saveCart(); // LÀM TRỐNG LOCALSTORAGE SAU KHI THANH TOÁN XONG
                    
//                     this.fetchMedicines(this.pagination.current_page); // Cập nhật lại tồn kho
//                 } else {
//                     let errorMsg = result.message || 'Có lỗi xảy ra.';
//                     if (result.errors) errorMsg = Object.values(result.errors).flat().join('\n');
//                     alert('❌ Lỗi thanh toán:\n' + errorMsg);
//                 }

//             } catch (error) {
//                 console.error("Lỗi Fetch API Checkout:", error);
//                 alert('❌ Mất kết nối đến máy chủ. Vui lòng thử lại!');
//             } finally {
//                 this.isProcessing = false;
//             }
//         }
//     }
// }


// Gắn function vào window để thẻ <body> có thể gọi x-data="posApp()"
window.posApp = function() {
    return {
        // --- 1. TRẠNG THÁI (STATE) ---
        openFilter: false, 
        
        // SỬA TẠI ĐÂY: Khôi phục giỏ hàng từ LocalStorage, nếu chưa có thì tạo mảng rỗng []
        cart: JSON.parse(localStorage.getItem('dyly_pos_cart')) || [],          
        
        isProcessing: false, 
        
        medicines: [],  
        isLoadingMedicines: false, 
        filters: {
            search: '', category: 'Tất cả', min_price: '', max_price: '', loai: '', stock: ''
        },
        pagination: {
            current_page: 1, last_page: 1, links: [] 
        },

        // State cho Khách hàng
        customerSearch: '',
        customerSearchResults: [],
        selectedCustomer: null,
        isSearchingCustomer: false,
        // --- THÊM CÁC BIẾN MỚI CHO MODAL ---
        showModal: false,
        isCreatingCustomer: false,
        newCustomer: { ten_khach_hang: '', so_dien_thoai: '' },

        init() {
            this.fetchMedicines(); 
        },

        // ==========================================
        // HÀM MỚI: LƯU GIỎ HÀNG XUỐNG TRÌNH DUYỆT
        // ==========================================
        saveCart() {
            localStorage.setItem('dyly_pos_cart', JSON.stringify(this.cart));
        },

        // --- 2. GỌI API LẤY DANH SÁCH THUỐC ---
        async fetchMedicines(page = 1) {
            this.isLoadingMedicines = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.category && this.filters.category !== 'Tất cả') params.append('category', this.filters.category);
                if (this.filters.min_price) params.append('min_price', this.filters.min_price);
                if (this.filters.max_price) params.append('max_price', this.filters.max_price);
                if (this.filters.loai) params.append('loai', this.filters.loai);
                if (this.filters.stock) params.append('stock', this.filters.stock);
                params.append('page', page);

                const response = await fetch(`/api/v1/pos/medicines?${params.toString()}`);
                const result = await response.json();

                if (result.status === 'success') {
                    this.medicines = result.data.data;
                    this.pagination = {
                        current_page: result.data.current_page,
                        last_page: result.data.last_page,
                        links: result.data.links
                    };
                }
            } catch (error) {
                console.error("Lỗi khi tải danh sách thuốc:", error);
            } finally {
                this.isLoadingMedicines = false;
            }
        },

        applyFilters() { this.fetchMedicines(1); },
        setCategory(categoryName) { this.filters.category = categoryName; this.fetchMedicines(1); },
        clearFilters() { 
            this.filters = { search: '', category: 'Tất cả', min_price: '', max_price: '', loai: '', stock: '' };
            this.fetchMedicines(1);
        },
        changePage(url) {
            if (!url) return;
            const urlObj = new URL(url, window.location.origin);
            const page = urlObj.searchParams.get('page') || 1;
            this.fetchMedicines(page);
        },

        // --- TÌM & TẠO KHÁCH HÀNG ---
        async searchCustomer() {
            // Bắt đầu tìm kiếm khi có ít nhất 2 ký tự
            if (this.customerSearch.trim().length < 2) {
                this.customerSearchResults = [];
                return;
            }
            
            this.isSearchingCustomer = true;
            try {
                const response = await fetch(`/api/v1/pos/customers/search?q=${this.customerSearch.trim()}`);
                const result = await response.json();
                if (result.status === 'success') {
                    this.customerSearchResults = result.data;
                }
            } catch (error) {
                console.error("Lỗi tìm khách hàng:", error);
            } finally {
                this.isSearchingCustomer = false;
            }
        },

        selectCustomer(customer) {
            console.log(customer);
            this.selectedCustomer = customer;
            this.customerSearch = '';
            this.customerSearchResults = [];
        },

        clearCustomer() {
            this.selectedCustomer = null;
        },

        // --- THÊM HÀM MỞ MODAL VÀ LƯU KHÁCH HÀNG (Thay thế cho hàm createNewCustomer cũ bằng prompt) ---
        openCustomerModal() {
            // Tự động điền SĐT nếu nhân viên đang gõ dở trên ô tìm kiếm
            this.newCustomer.so_dien_thoai = this.customerSearch.trim();
            this.newCustomer.ten_khach_hang = '';
            this.showModal = true;
        },

        async submitNewCustomer() {
            if (!this.newCustomer.ten_khach_hang || !this.newCustomer.so_dien_thoai) {
                alert('Vui lòng nhập đầy đủ tên và số điện thoại!');
                return;
            }

            this.isCreatingCustomer = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/api/v1/pos/customers/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify(this.newCustomer)
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    this.selectCustomer(result.data); // Gắn luôn vào bill
                    this.showModal = false;           // Tắt modal
                } else {
                    alert('❌ Không thể tạo: ' + (result.message || 'Số điện thoại có thể đã tồn tại.'));
                }
            } catch (error) {
                console.error("Lỗi tạo khách hàng:", error);
                alert('Lỗi kết nối đến máy chủ.');
            } finally {
                this.isCreatingCustomer = false;
            }
        },

        // --- 3. COMPUTED & TIỆN ÍCH ---
        get totalAmount() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫';
        },

        // --- 4. CÁC HÀM XỬ LÝ GIỎ HÀNG ---
        addToCart(thuoc) {
            if (thuoc.stock <= 0) {
                alert('Sản phẩm này đã hết hàng trong kho!');
                return;
            }

            let existingItem = this.cart.find(item => item.id === thuoc.id);
            if (existingItem) {
                if (existingItem.quantity < thuoc.stock) {
                    existingItem.quantity++;
                } else {
                    alert('Số lượng trong giỏ đã đạt giới hạn tồn kho!');
                }
            } else {
                this.cart.push({ ...thuoc, quantity: 1 });
            }
            
            // LƯU LẠI GIỎ HÀNG KHI THÊM MỚI
            this.saveCart();
        },

        increaseQuantity(id) {
            let item = this.cart.find(i => i.id === id);
            if (item && item.quantity < item.stock) {
                item.quantity++;
                this.saveCart(); // LƯU KHI TĂNG SỐ LƯỢNG
            }
        },

        decreaseQuantity(id) {
            let item = this.cart.find(i => i.id === id);
            if (item) {
                if (item.quantity > 1) {
                    item.quantity--;
                } else {
                    this.cart = this.cart.filter(i => i.id !== id);
                }
                this.saveCart(); // LƯU KHI GIẢM HOẶC XÓA SẢN PHẨM
            }
        },

        clearCart() {
            if (this.cart.length > 0 && confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
                this.cart = [];
                this.saveCart(); // LÀM TRỐNG LOCALSTORAGE
            }
        },

        // --- 5. HÀM THANH TOÁN ---
        async processCheckout() {
            if (this.cart.length === 0) return;
            if (!confirm(`Xác nhận thanh toán đơn hàng với tổng tiền: ${this.formatPrice(this.totalAmount)}?`)) return;

            this.isProcessing = true;
            try {
                console.log(this.selectedCustomer)

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/api/v1/pos/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({
                        cart: this.cart,
                        total_amount: this.totalAmount,
                        customer_id: this.selectedCustomer ? this.selectedCustomer.id : nullnull
                    })
                    
                });
                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // SỬA TẠI ĐÂY: Thay alert bằng confirm để tạo nút chọn In Bill
                    let inHoaDon = confirm(`✅ Thanh toán thành công! Mã đơn hàng: #HD${result.order_id}\n\nBạn có muốn IN HÓA ĐƠN ngay bây giờ không?`);
                    
                    if (inHoaDon) {
                        // result.order_id đang có dạng "0015", cần parse về số nguyên 15
                        let rawOrderId = parseInt(result.order_id, 10);
                        
                        // CẬP NHẬT ĐƯỜNG DẪN IN BILL RIÊNG CHO POS
                        // Mở popup nhỏ gọn thay vì tab lớn
                        window.open(`/pos/orders/${rawOrderId}/print`, 'PrintBill', 'width=600,height=800');
                    }
                    
                    this.cart = []; 
                    this.clearCustomer(); // Reset Khách hàng chuẩn bị cho đơn mới
                    this.saveCart(); // LÀM TRỐNG LOCALSTORAGE SAU KHI THANH TOÁN XONG
                    
                    this.fetchMedicines(this.pagination.current_page); // Cập nhật lại tồn kho
                } else {
                    let errorMsg = result.message || 'Có lỗi xảy ra.';
                    if (result.errors) errorMsg = Object.values(result.errors).flat().join('\n');
                    alert('❌ Lỗi thanh toán:\n' + errorMsg);
                }

            } catch (error) {
                console.error("Lỗi Fetch API Checkout:", error);
                alert('❌ Mất kết nối đến máy chủ. Vui lòng thử lại!');
            } finally {
                this.isProcessing = false;
            }
        }
    }
}