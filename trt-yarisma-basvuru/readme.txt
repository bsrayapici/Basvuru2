=== TRT Yarışma Başvuru Sistemi ===
Contributors: manus-ai
Tags: form, competition, application, trt, multi-step
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

TRT yarışmaları için çok adımlı başvuru formu sistemi.

== Description ==

TRT Yarışma Başvuru Sistemi, TRT tarafından düzenlenen yarışmalara başvuru yapmak isteyen kullanıcılar için geliştirilmiş profesyonel bir WordPress eklentisidir.

= Özellikler =

* **Çok Adımlı Form**: 4 adımlı kullanıcı dostu başvuru süreci
* **Kategori Tabanlı**: Farklı yarışma kategorileri için özelleştirilebilir formlar
* **Responsive Tasarım**: Mobil, tablet ve masaüstü uyumlu
* **Admin Panel**: Başvuruları yönetmek için kapsamlı admin paneli
* **E-posta Bildirimleri**: Otomatik onay ve durum güncelleme e-postaları
* **Excel Export**: Başvuruları Excel formatında dışa aktarma
* **SMTP Desteği**: Özelleştirilebilir SMTP ayarları
* **Güvenlik**: WordPress güvenlik standartlarına uygun

= Desteklenen Kategoriler =

* Proje Destek Yarışması
* Profesyonel Ulusal Belgesel Ödülleri Yarışması
* Öğrenci Ulusal Belgesel Ödülleri Yarışması
* International Competition

= Form Adımları =

1. **Eser Linki ve Bilgileri**: Program detayları, bütçe, çekim yerleri
2. **Eser Sahibi Bilgileri**: Kişisel bilgiler, özgeçmiş, deneyimler
3. **Katılım Sözleşmesi**: Sözleşme metni ve KVKK onayları
4. **Başvuru Özeti ve Onay**: Tüm bilgilerin özeti ve final onayı

== Installation ==

1. Eklenti dosyalarını `/wp-content/plugins/trt-yarisma-basvuru/` klasörüne yükleyin
2. WordPress admin panelinden eklentiyi etkinleştirin
3. `TRT Yarışma` menüsünden ayarları yapılandırın
4. Başvuru formunu göstermek istediğiniz sayfaya `[trt_yarisma_form]` shortcode'unu ekleyin

== Frequently Asked Questions ==

= Formu nasıl sayfama eklerim? =

Başvuru formunu göstermek istediğiniz sayfaya `[trt_yarisma_form]` shortcode'unu ekleyin.

= Belirli bir kategori için form gösterebilir miyim? =

Evet, shortcode'u şu şekilde kullanabilirsiniz: `[trt_yarisma_form kategori="proje-destek-yarismasi"]`

= E-posta bildirimleri nasıl çalışır? =

Başvuru tamamlandığında kullanıcıya otomatik onay e-postası gönderilir. Admin panelinden başvuru durumu değiştirildiğinde de bilgilendirme e-postası gönderilir.

= SMTP ayarlarını nasıl yapılandırırım? =

Admin panelinden `TRT Yarışma > Ayarlar` bölümünden SMTP ayarlarınızı yapılandırabilirsiniz.

= Başvuruları Excel'e nasıl aktarırım? =

Admin panelindeki başvuru listesinde "Excel'e Aktar" butonunu kullanabilirsiniz.

== Screenshots ==

1. Başvuru formu - Adım 1: Eser Linki ve Bilgileri
2. Başvuru formu - Adım 2: Eser Sahibi Bilgileri
3. Başvuru formu - Adım 3: Katılım Sözleşmesi
4. Başvuru formu - Adım 4: Başvuru Özeti
5. Admin panel - Başvuru listesi
6. Admin panel - Başvuru detayı
7. Admin panel - Ayarlar sayfası

== Changelog ==

= 1.0.0 =
* İlk sürüm
* Çok adımlı başvuru formu
* Admin panel yönetimi
* E-posta bildirimleri
* Excel export özelliği
* SMTP desteği

== Upgrade Notice ==

= 1.0.0 =
İlk sürüm - yeni kurulum.

== Technical Requirements ==

* WordPress 5.0 veya üzeri
* PHP 7.4 veya üzeri
* MySQL 5.6 veya üzeri

== Support ==

Destek için geleceginiletisilmcileri@trt.net.tr adresine yazabilirsiniz.

== Privacy Policy ==

Bu eklenti kullanıcı verilerini yalnızca başvuru sürecinde toplar ve saklar. Veriler üçüncü taraflarla paylaşılmaz.

== Credits ==

Bu eklenti Manus AI tarafından TRT için geliştirilmiştir.

