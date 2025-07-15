# TRT Yarışma Başvuru Sistemi - Kurulum Talimatları

## Sistem Gereksinimleri

- WordPress 5.0 veya üzeri
- PHP 7.4 veya üzeri
- MySQL 5.6 veya üzeri
- Aktif WordPress teması

## Kurulum Adımları

### 1. Eklenti Dosyalarını Yükleme

1. `trt-yarisma-basvuru` klasörünü WordPress sitenizin `/wp-content/plugins/` dizinine kopyalayın
2. WordPress admin paneline giriş yapın
3. **Eklentiler** > **Yüklü Eklentiler** sayfasına gidin
4. "TRT Yarışma Başvuru Sistemi" eklentisini bulun ve **Etkinleştir** butonuna tıklayın

### 2. İlk Yapılandırma

Eklenti etkinleştirildikten sonra:

1. Sol menüde **TRT Yarışma** menüsü görünecektir
2. **TRT Yarışma** > **Ayarlar** sayfasına gidin
3. E-posta ayarlarını yapılandırın (isteğe bağlı)

### 3. SMTP E-posta Ayarları (İsteğe Bağlı)

Otomatik e-posta gönderimi için SMTP ayarlarını yapılandırabilirsiniz:

1. **TRT Yarışma** > **Ayarlar** sayfasına gidin
2. SMTP bilgilerinizi girin:
   - **SMTP Host**: Mail sunucunuzun adresi (örn: smtp.gmail.com)
   - **SMTP Port**: Port numarası (genellikle 587 veya 465)
   - **SMTP Kullanıcı Adı**: E-posta adresiniz
   - **SMTP Şifre**: E-posta şifreniz veya uygulama şifresi
   - **Şifreleme**: TLS veya SSL
   - **Gönderen E-posta**: Gönderen e-posta adresi
   - **Gönderen Adı**: Gönderen adı

3. **Değişiklikleri Kaydet** butonuna tıklayın

### 4. Başvuru Formunu Sayfaya Ekleme

1. WordPress admin panelinde **Sayfalar** > **Yeni Ekle** veya mevcut bir sayfayı düzenleyin
2. Sayfa içeriğine aşağıdaki shortcode'u ekleyin:

```
[trt_yarisma_form]
```

Belirli bir kategori için form göstermek isterseniz:

```
[trt_yarisma_form kategori="proje-destek-yarismasi"]
```

3. Sayfayı kaydedin ve yayınlayın

## Kullanılabilir Kategoriler

- `proje-destek-yarismasi` - Proje Destek Yarışması
- `profesyonel-ulusal-belgesel` - Profesyonel Ulusal Belgesel Ödülleri Yarışması
- `ogrenci-ulusal-belgesel` - Öğrenci Ulusal Belgesel Ödülleri Yarışması
- `international-competition` - International Competition

## Admin Panel Kullanımı

### Başvuruları Görüntüleme

1. **TRT Yarışma** > **Başvurular** sayfasına gidin
2. Tüm başvuruları listede görebilirsiniz
3. Filtreleme seçeneklerini kullanarak belirli kategorilere veya durumlara göre filtreleyebilirsiniz

### Başvuru Detaylarını İnceleme

1. Başvuru listesinde **Görüntüle** butonuna tıklayın
2. Başvurunun tüm detaylarını görebilirsiniz

### Başvuru Durumunu Güncelleme

1. Başvuru listesinde durum dropdown'ından yeni durumu seçin
2. Durum otomatik olarak güncellenecek ve başvuru sahibine e-posta gönderilecektir

### Excel'e Aktarma

1. Başvuru listesinde **Excel'e Aktar** butonuna tıklayın
2. Filtrelenmiş başvurular CSV formatında indirilecektir

## Veritabanı Tabloları

Eklenti aşağıdaki tabloları oluşturur:

- `wp_trt_yarisma_basvurular` - Başvuru verileri
- `wp_trt_yarisma_kategoriler` - Kategori bilgileri

## Güvenlik

- Tüm form verileri sanitize edilir
- CSRF koruması için nonce kullanılır
- Admin işlemleri için yetki kontrolü yapılır
- SQL injection koruması

## Sorun Giderme

### E-postalar Gönderilmiyor

1. SMTP ayarlarını kontrol edin
2. WordPress'in wp_mail() fonksiyonunun çalıştığından emin olun
3. Sunucu e-posta ayarlarını kontrol edin

### Form Görünmüyor

1. Shortcode'un doğru yazıldığından emin olun
2. Eklentinin etkin olduğunu kontrol edin
3. Tema ile uyumluluk sorunları olup olmadığını kontrol edin

### Veritabanı Hataları

1. WordPress veritabanı bağlantısını kontrol edin
2. Kullanıcının veritabanı yazma yetkisi olduğundan emin olun

## Destek

Teknik destek için:
- E-posta: geleceginiletisilmcileri@trt.net.tr

## Güncelleme

Eklenti güncellenirken:

1. Mevcut eklenti dosyalarını yedekleyin
2. Veritabanını yedekleyin
3. Yeni dosyaları yükleyin
4. Eklentiyi yeniden etkinleştirin

## Kaldırma

Eklentiyi tamamen kaldırmak için:

1. Eklentiyi devre dışı bırakın
2. Eklenti dosyalarını silin
3. İsterseniz veritabanı tablolarını manuel olarak silebilirsiniz:
   - `wp_trt_yarisma_basvurular`
   - `wp_trt_yarisma_kategoriler`

