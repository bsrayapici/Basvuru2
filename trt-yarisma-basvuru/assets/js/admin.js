/**
 * TRT Yarışma Başvuru Formu - Admin Panel JavaScript
 */

jQuery(document).ready(function($) {
    
    // Durum güncelleme AJAX handler'ı ekle
    if (typeof updateApplicationStatus === 'undefined') {
        window.updateApplicationStatus = function(id, status) {
            $.post(ajaxurl, {
                action: 'trt_yarisma_update_status',
                id: id,
                status: status,
                nonce: $('#trt-admin-nonce').val()
            }, function(response) {
                if (response.success) {
                    // Başarı mesajı göster
                    showNotice('Durum başarıyla güncellendi.', 'success');
                } else {
                    // Hata mesajı göster
                    showNotice('Durum güncellenirken hata oluştu.', 'error');
                    // Select'i eski haline döndür
                    location.reload();
                }
            }).fail(function() {
                showNotice('Bağlantı hatası oluştu.', 'error');
                location.reload();
            });
        };
    }
    
    // Bildirim gösterme fonksiyonu
    function showNotice(message, type) {
        var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        var notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap h1').after(notice);
        
        // 3 saniye sonra otomatik kaldır
        setTimeout(function() {
            notice.fadeOut();
        }, 3000);
    }
    
    // Toplu işlemler
    $('#doaction, #doaction2').click(function(e) {
        var action = $(this).siblings('select').val();
        var checked = $('input[name="application[]"]:checked');
        
        if (action === '-1') {
            e.preventDefault();
            return false;
        }
        
        if (checked.length === 0) {
            e.preventDefault();
            alert('Lütfen en az bir başvuru seçiniz.');
            return false;
        }
        
        if (action === 'delete') {
            if (!confirm('Seçili başvuruları silmek istediğinizden emin misiniz?')) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Arama filtresi
    var searchTimeout;
    $('#application-search').on('input', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val();
        
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                filterApplications(searchTerm);
            }
        }, 500);
    });
    
    function filterApplications(searchTerm) {
        var rows = $('.wp-list-table tbody tr');
        
        if (searchTerm === '') {
            rows.show();
            return;
        }
        
        rows.each(function() {
            var row = $(this);
            var text = row.text().toLowerCase();
            
            if (text.indexOf(searchTerm.toLowerCase()) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    }
    
    // Excel export onay
    $('a[href*="trt_yarisma_export"]').click(function(e) {
        if (!confirm('Excel dosyasını indirmek istediğinizden emin misiniz?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Form validasyonu (ayarlar sayfası için)
    $('#trt-settings-form').submit(function(e) {
        var smtpHost = $('input[name="smtp_host"]').val();
        var smtpUsername = $('input[name="smtp_username"]').val();
        var fromEmail = $('input[name="from_email"]').val();
        
        if (smtpHost && !smtpUsername) {
            e.preventDefault();
            alert('SMTP host belirtildiğinde kullanıcı adı da gereklidir.');
            return false;
        }
        
        if (fromEmail && !isValidEmail(fromEmail)) {
            e.preventDefault();
            alert('Geçerli bir e-posta adresi giriniz.');
            return false;
        }
    });
    
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Tooltip'ler
    $('[title]').each(function() {
        $(this).tooltip();
    });
    
    // Sütun sıralama
    $('.wp-list-table th.sortable a').click(function(e) {
        e.preventDefault();
        var url = new URL($(this).attr('href'));
        var orderby = url.searchParams.get('orderby');
        var order = url.searchParams.get('order') || 'asc';
        
        // Mevcut URL'ye parametreleri ekle
        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set('orderby', orderby);
        currentUrl.searchParams.set('order', order === 'asc' ? 'desc' : 'asc');
        
        window.location.href = currentUrl.toString();
    });
});

// AJAX durum güncelleme için nonce ekle
jQuery(document).ready(function($) {
    if (!$('#trt-admin-nonce').length) {
        $('body').append('<input type="hidden" id="trt-admin-nonce" value="' + trt_yarisma_admin.nonce + '" />');
    }
});

