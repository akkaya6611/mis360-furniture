<?php
/**
 * Emdief Home Kurumsal Sayfalar & Yasal Politikalar Motoru
 *
 * @package Mis360-Mobilya
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Kurumsal Sayfa Tanımları ve Şablon İçerikleri
 */
function mis360_get_corporate_pages_data() {
    return [
        'cerez-politikasi' => [
            'title'   => 'Çerez Politikası',
            'content' => '<div class="corporate-content-block">
    <div class="prose-alert prose-alert-info">
        <span class="alert-icon">🍪</span>
        <div class="alert-body">
            <strong>Bilgilendirme:</strong> Bu Çerez Politikası metni; Emdief Home web sitesinde (https://emdiefhome.com.tr) ziyaretçilerimizin gizliliğini korumak ve güvenli bir alışveriş deneyimi sunmak amacıyla hazırlanmıştır.
        </div>
    </div>

    <h2>1. Çerez (Cookie) Nedir?</h2>
    <p>Çerezler (cookies), web sitelerini ziyaret ettiğinizde bilgisayarınız, tabletiniz veya akıllı telefonunuz gibi internete erişim sağlayan cihazlarınıza kaydedilen küçük metin dosyalarıdır. Çerezler, web sitelerinin daha verimli çalışmasını, oturumunuzun ve sepetinizin korunmasını ve tercihlerinize uygun kişiselleştirilmiş bir alışveriş deneyimi yaşamanızı sağlar.</p>

    <h2>2. Emdief Home Çerezleri Hangi Amaçlarla Kullanır?</h2>
    <p>Emdief Home olarak sitemizde çerezleri aşağıdaki temel amaçlarla kullanmaktayız:</p>
    <ul>
        <li><strong>Alışveriş Sepetinin ve Oturumun Korunması:</strong> Beğendiğiniz Montessori çocuk mobilyalarını sepete eklediğinizde, sayfalar arasında gezinseniz veya tarayıcınızı kapatıp tekrar açsanız dahi sepetinizin kaybolmamasını sağlamak.</li>
        <li><strong>Site Güvenliği ve Doğrulama:</strong> Kötü niyetli girişimleri engellemek, 256-bit SSL güvenlik sertifikası kapsamında güvenli veri aktarımı sağlamak.</li>
        <li><strong>Kullanıcı Tercihleri:</strong> Para birimi (TL), filtreleme tercihleri ve son incelediğiniz ürünlerin listelenmesi gibi kişisel seçimlerinizi hatırlamak.</li>
        <li><strong>Performans ve Hız Optimizasyonu:</strong> Sitemizin en çok ziyaret edilen bölümlerini, sayfa yüklenme hızlarını ve teknik hataları anonim olarak analiz ederek web sitemizin hızını artırmak.</li>
    </ul>

    <h2>3. Sitemizde Kullanılan Çerez Türleri</h2>
    <table class="corporate-table">
        <thead>
            <tr>
                <th>Çerez Kategorisi</th>
                <th>Açıklama ve Kullanım Amacı</th>
                <th>Saklama Süresi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Zorunlu Çerezler</strong></td>
                <td>Sitenin temel işlevlerini yerine getirebilmesi, sepet mekanizması ve ödeme adımları için zorunludur. Devre dışı bırakılamaz.</td>
                <td>Oturum Boyunca</td>
            </tr>
            <tr>
                <td><strong>İşlevsel Çerezler</strong></td>
                <td>Son gezilen ürünler (emdief_recently_viewed) ve kullanıcı tercihlerinin hatırlanmasını sağlar.</td>
                <td>30 Gün</td>
            </tr>
            <tr>
                <td><strong>Analitik Çerezler</strong></td>
                <td>Ziyaretçi trafiğini, en popüler kategorileri ve sayfa performansını anonim istatistiklerle ölçer.</td>
                <td>Kalıcı / 2 Yıl</td>
            </tr>
            <tr>
                <td><strong>Pazarlama Çerezleri</strong></td>
                <td>İlgi alanlarınıza uygun ürün ve kampanyaların gösterilmesi amacıyla sınırlı olarak kullanılır.</td>
                <td>Dönemsel</td>
            </tr>
        </tbody>
    </table>

    <h2>4. Çerezleri Nasıl Yönetebilir veya Kapatabilirsiniz?</h2>
    <p>Tarayıcınızın ayarlarını değiştirerek çerez tercihlerinizi dilediğiniz an yönetebilir veya tüm çerezleri silebilirsiniz. Ancak zorunlu çerezlerin kapatılması durumunda sepetinize ürün ekleme, sipariş oluşturma ve üye girişi gibi kritik işlevler çalışmayabilir.</p>
    <div class="browser-links-box">
        <strong>Popüler Tarayıcılarda Çerez Ayarları:</strong>
        <ul>
            <li><strong>Google Chrome:</strong> Ayarlar &gt; Gizlilik ve Güvenlik &gt; Çerezler ve Diğer Site Verileri</li>
            <li><strong>Apple Safari:</strong> Tercihler &gt; Gizlilik &gt; Tüm Çerezleri Engelle / Yönet</li>
            <li><strong>Mozilla Firefox:</strong> Seçenekler &gt; Gizlilik ve Güvenlik &gt; Çerezler ve Site Verileri</li>
            <li><strong>Microsoft Edge:</strong> Ayarlar &gt; Çerezler ve Site İzinleri &gt; Çerezleri Yönet</li>
        </ul>
    </div>

    <h2>5. İletişim ve Haklarınız</h2>
    <p>Çerez politikamız ile ilgili her türlü soru ve talepleriniz için <a href="mailto:info@emdiefhome.com.tr">info@emdiefhome.com.tr</a> e-posta adresimiz veya <strong>+90 537 477 87 66</strong> numaralı destek hattımız üzerinden bizimle iletişime geçebilirsiniz.</p>
</div>',
        ],
        'gizlilik-ve-kvkk' => [
            'title'   => 'Gizlilik Politikası ve KVKK Aydınlatma Metni',
            'content' => '<div class="corporate-content-block">
    <div class="prose-alert prose-alert-info">
        <span class="alert-icon">🛡️</span>
        <div class="alert-body">
            <strong>Veri Sorumlusu:</strong> Emdief Home (Orhan TEBER) - Mobilya Kent Kırmızı Bloklar, Camikebir Mah. 5066. Sk No:1 D:K, 38070 Kocasinan / Kayseri; 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca kişisel verilerinizin güvenliğine ve gizliliğine azami hassasiyet göstermektedir.
        </div>
    </div>

    <h2>1. Kişisel Verilerin İşlenme Amacı ve Hukuki Sebebi</h2>
    <p>Emdief Home web sitesi üzerinden alışveriş yapan veya sitemizi ziyaret eden müşterilerimizin kişisel verileri; 6698 sayılı KVKK\'nın 5. ve 6. maddelerinde belirtilen kişisel veri işleme şartları dahilinde aşağıdaki amaçlarla işlenmektedir:</p>
    <ul>
        <li>Montessori çocuk mobilyası siparişlerinizin imalatının planlanması, paketlenmesi ve adresinize teslim edilmesi,</li>
        <li>6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği kapsamındaki yükümlülüklerimizin yerine getirilmesi,</li>
        <li>Elektronik fatura / e-arşiv fatura düzenlenmesi ve yasal muhasebe kayıtlarının tutulması,</li>
        <li>Kargo ve lojistik süreçlerinin takibi, teslimat bildirimlerinin (SMS / E-posta / WhatsApp) iletilmesi,</li>
        <li>Satış sonrası montaj, destek, yedek parça ve garanti süreçlerinin yönetilmesi.</li>
    </ul>

    <h2>2. İşlenen Kişisel Veri Kategorileri</h2>
    <ul>
        <li><strong>Kimlik Bilgileri:</strong> Ad, soyad, T.C. kimlik numarası (fatura zorunluluğu durumunda).</li>
        <li><strong>İletişim Bilgileri:</strong> Teslimat adresi, fatura adresi, e-posta adresi, cep telefonu numarası.</li>
        <li><strong>Müşteri İşlem Bilgileri:</strong> Satın alınan ürünler, sipariş tarihi, ödeme şekli, talep ve şikayet geçmişi.</li>
        <li><strong>İşlem Güvenliği Bilgileri:</strong> IP adresi, site giriş-çıkış log kayıtları, çerez kayıtları.</li>
    </ul>

    <h2>3. Güvenli Ödeme (Banka Havalesi / EFT / FAST) Taahhüdü</h2>
    <div class="prose-alert prose-alert-success">
        <span class="alert-icon">🏦</span>
        <div class="alert-body">
            <strong>Banka Havalesi / EFT Güvencesi:</strong> Sitemiz üzerinden verilen tüm siparişlerin ödemeleri doğrudan resmi şirket banka hesaplarımıza <u>Banka Havalesi, EFT veya FAST</u> yöntemiyle güvenli bir biçimde gerçekleştirilmektedir. Web sitemiz üzerinde müşterilerimizden herhangi bir kredi kartı veya banka kartı bilgisi <u>kesinlikle talep edilmemekte ve saklanmamaktadır</u>. Sipariş onaylandıktan sonra ödemeler, müşterinin kendi bankacılık kanalları (mobil bankacılık veya internet şubesi) üzerinden doğrudan şirket banka hesaplarımıza güvenle transfer edilir.
        </div>
    </div>

    <h2>4. Kişisel Verilerin Aktarılması</h2>
    <p>Kişisel verileriniz, yukarıda sayılan amaçların gerçekleştirilmesi doğrultusunda yalnızca:</p>
    <ul>
        <li>Siparişlerinizin adresinize ulaştırılması için <strong>anlaşmalı kargo şirketlerine</strong>,</li>
        <li>Yasal mevzuat gereği faturalandırma için <strong>Gelir İdaresi Başkanlığı</strong> ve yetkili kamu kurumlarına,</li>
        <li>Hukuki uyuşmazlık durumunda adli mercilere aktarılmaktadır. Verileriniz hiçbir surette üçüncü taraflara pazarlama veya reklam amacıyla satılmaz ya da kiralanmaz.</li>
    </ul>

    <h2>5. KVKK Madde 11 Kapsamındaki Haklarınız</h2>
    <p>KVKK\'nın 11. maddesi gereğince veri sahibi olarak; verilerinizin işlenip işlenmediğini öğrenme, işlenmişse bilgi talep etme, eksik veya yanlış işlenmişse düzeltilmesini isteme ve silinmesini talep etme hakkına sahipsiniz. Başvurularınızı <a href="mailto:info@emdiefhome.com.tr">info@emdiefhome.com.tr</a> adresine iletebilirsiniz.</p>
</div>',
        ],
        'mesafeli-satis-sozlesmesi' => [
            'title'   => 'Mesafeli Satış Sözleşmesi',
            'content' => '<div class="corporate-content-block">
    <div class="prose-alert prose-alert-info">
        <span class="alert-icon">📝</span>
        <div class="alert-body">
            <strong>Yasal Dayanak:</strong> İşbu sözleşme; 6502 Sayılı Tüketicinin Korunması Hakkında Kanun ve 29188 Sayılı Mesafeli Sözleşmeler Yönetmeliği hükümleri gereğince düzenlenmiştir.
        </div>
    </div>

    <h2>MADDE 1 – TARAFLAR</h2>
    <p><strong>1.1. SATICI:</strong><br>
    Unvan: Emdief Home (Orhan TEBER)<br>
    Adres: Mobilya Kent Kırmızı Bloklar, Camikebir Mahallesi, 5066. Sk No:1 D:K, 38070 Kocasinan / Kayseri<br>
    Telefon: +90 537 477 87 66<br>
    E-posta: info@emdiefhome.com.tr<br>
    Web: https://emdiefhome.com.tr</p>

    <p><strong>1.2. ALICI:</strong><br>
    Web sitesinden sipariş oluşturan, fatura ve teslimat bilgilerini giren gerçek veya tüzel kişi ("Alıcı").</p>

    <h2>MADDE 2 – SÖZLEŞMENİN KONUSU</h2>
    <p>İşbu sözleşmenin konusu; Alıcı\'nın Satıcı\'ya ait web sitesi üzerinden elektronik ortamda siparişini verdiği, sitede nitelikleri ve satış fiyatı belirtilen 1. sınıf MDF Montessori çocuk mobilyaları (Montessori kitaplıklar, eğitici ahşap oyuncaklar, oyuncak ve eşya düzenleyiciler, açık askılıklar ve duvar rafları) ve tamamlayıcı çocuk odası ürünlerinin satışı ve teslimi ile ilgili olarak tarafların hak ve yükümlülüklerinin belirlenmesidir.</p>

    <h2>MADDE 3 – SİPARİŞ VE ÖDEME KOŞULLARI</h2>
    <ul>
        <li>Ürünlerin cinsi, modeli, rengi, adedi, KDV dahil satış bedeli ve kargo ücreti sipariş özeti ekranında ve sipariş onay e-postasında belirtildiği gibidir.</li>
        <li>Satıcı, sitede duyurulan kampanya koşullarına bağlı olarak 1.500 TL ve üzeri siparişlerde kargo ücretini üstlenmektedir.</li>
        <li><strong>Ödeme Yöntemi:</strong> Sitemizde sipariş bedeli ödemeleri doğrudan Satıcı\'nın resmi banka hesabına <strong>Banka Havalesi, EFT veya FAST</strong> yöntemiyle gerçekleştirilmektedir. Sipariş tamamlandığında ekranda ve sipariş onay e-postasında Alıcı\'ya Satıcı\'nın IBAN ve banka hesap bilgileri sunulur. Alıcı, sipariş numarasını açıklama kısmına yazarak transferi tamamlar.</li>
    </ul>

    <h2>MADDE 4 – TESLİMAT ESASLARI</h2>
    <ul>
        <li>Sipariş konusu ürünler, sipariş onayından itibaren yasal 30 günlük süreyi aşmamak koşuluyla, siparişin imalat ve hazırlık süresine göre Alıcı\'nın belirlediği adrese teslim edilir.</li>
        <li>Ürünler demonte olarak, özel korumalı mukavva koli ve strafor destekleriyle güvenli bir biçimde sevk edilir. Koli içerisinde kurulum şeması ve montaj vidaları yer almaktadır.</li>
    </ul>

    <h2>MADDE 5 – CAYMA HAKKI</h2>
    <ul>
        <li>Alıcı, sözleşme konusu ürünün kendisine veya gösterdiği adresteki kişi/kuruluşa tesliminden itibaren <strong>14 (on dört) gün</strong> içinde hiçbir gerekçe göstermeksizin ve cezai şart ödemeksizin sözleşmeden cayma hakkına sahiptir.</li>
        <li>Cayma hakkının kullanılması için bu süre içinde Satıcı\'ya faks, e-posta veya telefon ile yazılı bildirimde bulunulması ve ürünün kullanılmamış, hasar görmemiş ve orijinal ambalajının bozulmamış olması şarttır.</li>
    </ul>

    <h2>MADDE 6 – CAYMA HAKKI KULLANILAMAYACAK ÜRÜNLER</h2>
    <ul>
        <li>Alıcı\'nın özel istek ve talepleri uyarınca üretilen veya üzerinde değişiklik/ilaveler yapılarak kişiye özel hale getirilen ürünler.</li>
        <li>Montajı/kurulumu yapılmış, vidalanmış, tekrar sıfır ürün olarak satılabilirlik vasfını yitirmiş mobilyalar.</li>
    </ul>

    <h2>MADDE 7 – UYUŞMAZLIKLARIN ÇÖZÜMÜ</h2>
    <p>İşbu sözleşmeden doğabilecek uyuşmazlıklarda; Ticaret Bakanlığı\'nca ilan edilen değere kadar Tüketici Hakem Heyetleri, bu değerin üzerindeki durumlarda ise Alıcı\'nın veya Satıcı\'nın yerleşim yerindeki Tüketici Mahkemeleri yetkilidir.</p>
</div>',
        ],
        'teslimat-ve-iade' => [
            'title'   => 'Teslimat, İptal ve İade Koşulları',
            'content' => '<div class="corporate-content-block">
    <div class="prose-alert prose-alert-warning">
        <span class="alert-icon">📦</span>
        <div class="alert-body">
            <strong>Kargo Teslimatında Çok Önemli Kural:</strong> Siparişinizi kargo görevlisinden teslim alırken kolide herhangi bir yırtılma, ezilme veya ıslanma fark ederseniz lütfen kargoyu teslim almayıp görevliye <strong>"Hasar Tespit Tutanağı"</strong> düzenletiniz.
        </div>
    </div>

    <h2>1. Kargo ve Teslimat Süreci</h2>
    <ul>
        <li><strong>Türkiye\'nin 81 İline Güvenli Gönderim:</strong> Tüm ürünlerimiz Türkiye genelinde anlaşmalı kurumsal kargo şirketleri aracılığıyla kapınıza kadar güvenle teslim edilmektedir.</li>
        <li><strong>Ücretsiz Kargo Avantajı:</strong> Emdief Home mağazasında <strong>1.500 TL ve üzeri</strong> tüm siparişlerde kargo ücreti tamamen tarafımızca karşılanmaktadır.</li>
        <li><strong>Özel Koruyucu Ambalaj:</strong> 1. sınıf MDF Montessori mobilyalarımız, taşıma esnasında köşelerin ve yüzeylerin darbe almaması için güçlendirilmiş çift dalga mukavva koli, köşe koruyucu takozlar ve strafor desteklerle özenle paketlenir.</li>
        <li><strong>Kargoya Veriliş Süresi:</strong> Ürünlerimizin bir kısmı öncelikli stoklu, bir kısmı ise siparişe özel hassas CNC kesim ve zımparalama süreçlerinden geçerek <strong>1 ila 4 iş günü</strong> içerisinde kargoya teslim edilmektedir.</li>
    </ul>

    <h2>2. Sipariş İptali</h2>
    <p>Siparişinizi henüz kargoya teslim edilmeden önce iptal etmek isterseniz, <strong>+90 537 477 87 66</strong> numaralı destek hattımızı arayarak veya WhatsApp üzerinden sipariş numaranızı bildirerek anında ücretsiz iptal edebilirsiniz. Havale / EFT ile yapmış olduğunuz ödeme, bildireceğiniz banka IBAN hesabınıza aynı gün eksiksiz olarak geri transfer edilir.</p>

    <h2>3. İade ve Değişim Koşulları (14 Günlük Yasal Süreç)</h2>
    <p>Satın aldığınız ürünü teslim aldığınız tarihten itibaren <strong>14 gün içerisinde</strong> herhangi bir gerekçe göstermeksizin iade edebilirsiniz. İade işleminin sorunsuz tamamlanabilmesi için aşağıdaki kurallara dikkat edilmelidir:</p>
    <ul>
        <li>Ürünün demonte halde, tüm vida, aksesuar ve montaj kılavuzuyla birlikte eksiksiz olması,</li>
        <li>Orijinal koruyucu ambalajının ve kutusunun sağlam olması,</li>
        <li>Ürünün vidalanmamış, yapıştırılmamış veya montajı denenerek delik/çizik oluşturulmamış olması (tekrar sıfır olarak satılabilirlik vasfını koruması) gerekmektedir.</li>
    </ul>

    <h2>4. Hasarlı Parça / Eksik Parça Garantisi</h2>
    <div class="prose-alert prose-alert-success">
        <span class="alert-icon">🛠️</span>
        <div class="alert-body">
            <strong>Ücretsiz Yedek Parça Desteği:</strong> Kargonuzu açtığınızda herhangi bir parçada çatlak, çizik veya eksik vida fark ederseniz ürünü tamamen geri göndermekle uğraşmanıza gerek yoktur! Parçanın fotoğrafını WhatsApp hattımıza (+90 537 477 87 66) gönderdiğinizde, <strong>hasarlı parça aynı gün ücretsiz olarak</strong> adresinize yeni olarak kargolanır.
        </div>
    </div>

    <h2>5. İade Ücretinin Geri Ödenmesi</h2>
    <p>İade ettiğiniz ürün depomuza ulaştıktan sonra teknik ekibimizce incelenir ve iade koşullarına uygunluğu onaylandığı gün, ödemeyi yaptığınız banka hesabınıza (IBAN) Havale / EFT yöntemiyle eksiksiz olarak geri aktarılır.</p>
</div>',
        ],
        'hakkimizda' => [
            'title'   => 'Hakkımızda',
            'content' => '<div class="corporate-content-block">
    <div class="about-hero-box">
        <h2>Miniklerin Dünyasına Düzen, Özgüven ve Neşe Katıyoruz!</h2>
        <p class="lead-p">Emdief Home; Dr. Maria Montessori\'nin <em>"Çocuğun kendi kendine yapabilmesi için ona rehberlik edin"</em> felsefesinden ilham alarak, miniklerin bağımsız keşiflerini destekleyen 1. sınıf MDF çocuk odası mobilyaları ve Montessori ürünleri tasarlar ve üretir.</p>
    </div>

    <h2>Montessori Felsefesi Neden Bu Kadar Önemli?</h2>
    <p>Geleneksel mobilyalar genellikle yetişkinlerin boyuna ve ihtiyaçlarına göre tasarlanır; bu durum çocukların istedikleri kitaba, oyuncağa veya çalışma alanına erişmek için sürekli bir büyükten yardım istemesine yol açar. Emdief Home olarak biz, mobilyalarımızın boyutlarını çocukların doğal ergonomisine ve bağımsız gelişim metoduna göre planlıyoruz:</p>
    <ul>
        <li><strong>Erişilebilir Montessori Kitaplıklar:</strong> Kitapların kapaklarının doğrudan çocuğa baktığı ön yüzlü raflarımız sayesinde çocuklar henüz okuma yazma bilmeseler dahi kapak resmini görerek istedikleri kitabı kendi başlarına seçer ve yerine koyar.</li>
        <li><strong>Eğitici Ahşap Çocuk Oyuncakları:</strong> Motor becerilerini, el-göz koordinasyonunu ve yaratıcılığı geliştiren %100 doğal ahşap eğitici materyaller ve oyun arkadaşları.</li>
        <li><strong>Duvar ve Banyo Rafları:</strong> Çocukların boy hizasına uygun, emniyetli koruma barlı ve dekoratif montajlı estetik raf çözümleri.</li>
        <li><strong>Montessori Oyuncak Düzenleyiciler & Saklama Çözümleri:</strong> Çocukların kendi boy seviyesinde oyuncaklarını kategorize etmelerini ve odalarını toplama alışkanlığı kazanmalarını sağlayan fonksiyonel bölmeler.</li>
        <li><strong>Montessori Çocuk Gardıropları & Açık Askılıklar:</strong> Çocukların kendi kıyafetlerini seçip asabilmelerine olanak tanıyan, bağımsız giyinme becerisini geliştiren alçak askı sistemleri.</li>
    </ul>

    <h2>Neden Sadece 1. Sınıf Kaliteli MDF?</h2>
    <p>Çocuk mobilyasında malzeme seçimi hayati önem taşır. Sunta veya kalitesiz kaplamalar kolayca kırılabilir, nemden şişebilir veya çocukların ellerine kıymık batma riski barındırabilir. Emdief Home ürünlerinin tamamında:</p>
    <ul>
        <li><strong>1. Sınıf Yoğun MDF:</strong> Homojen, yoğun ve kırılmaya karşı mukavemetli ahşap gövde.</li>
        <li><strong>Yuvarlatılmış Güvenli Hatlar:</strong> Çocukların hareketli anlarında çarpma riskini en aza indirmek için hiçbir ürünümüzde keskin veya sivri köşe bırakılmamaktadır. Tüm kenarlar hassas CNC ve zımpara işlemiyle kavisli hale getirilir.</li>
        <li><strong>Kolay Silinebilir Pürüzsüz Yüzey:</strong> Çocukların pastel boya veya su lekelerini nemli bir bezle zahmetsizce temizleyebilirsiniz.</li>
    </ul>

    <h2>Yerli Üretim ve Atölye Gücü</h2>
    <p>Tasarımından hassas kesimine, zımparalamasından koruyucu paketlemesine kadar tüm süreçler Türkiye\'deki kendi modern tesislerimizde titizlikle yürütülmektedir. Binlerce mutlu ailenin ve çocuğun odasına güven ve estetik katmaktan gurur duyuyoruz.</p>
</div>',
        ],
        'iletisim' => [
            'title'   => 'İletişim & Müşteri Hizmetleri',
            'content' => '<div class="corporate-content-block">
    <p class="lead-p">Montessori mobilyalarımız, sipariş takibi veya özel taleplerinizle ilgili aklınıza takılan her konuda güler yüzlü ekibimize doğrudan ulaşabilirsiniz.</p>

    <div class="contact-cards-grid">
        <div class="contact-info-card">
            <div class="card-icon">📞</div>
            <h3>Müşteri Destek Hattı</h3>
            <p>Hafta içi ve Cumartesi 09:00 – 18:30 arası kesintisiz telefon desteği.</p>
            <a href="tel:+905374778766" class="contact-link">+90 537 477 87 66</a>
        </div>

        <div class="contact-info-card is-wa">
            <div class="card-icon">💬</div>
            <h3>Canlı WhatsApp Danışma</h3>
            <p>Ürün ölçüleri, montaj ve kargo durumu için anında mesaj gönderin.</p>
            <a href="https://wa.me/905374778766" target="_blank" rel="noopener" class="contact-link">Hemen WhatsApp\'tan Yazın</a>
        </div>

        <div class="contact-info-card">
            <div class="card-icon">✉️</div>
            <h3>Kurumsal E-Posta</h3>
            <p>Resmi yazışmalar, kurumsal teklif ve toptan satış talepleriniz için.</p>
            <a href="mailto:info@emdiefhome.com.tr" class="contact-link">info@emdiefhome.com.tr</a>
        </div>

        <div class="contact-info-card">
            <div class="card-icon">🏭</div>
            <h3>Fabrika Satış & Atölye</h3>
            <p>Mobilya Kent Kırmızı Bloklar, Camikebir Mah. 5066. Sk No:1 D:K, 38070 Kocasinan / Kayseri</p>
            <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0x152b057da63cc6c7:0x45e8ad2179bc179c?sa=X&ved=1t:8290&ictx=111" target="_blank" rel="noopener" class="contact-link">📍 Haritada Aç & Yol Tarifi Al &rarr;</a>
        </div>
    </div>

    <div class="prose-alert prose-alert-info mt-6">
        <span class="alert-icon">⏰</span>
        <div class="alert-body">
            <strong>Çalışma Saatlerimiz:</strong> Pazartesi – Cumartesi günleri 09:00 – 18:30 saatleri arasında destek verilmektedir. Pazar günleri WhatsApp üzerinden iletilen mesajlara ilk iş günü sabahında sırayla yanıt verilmektedir.
        </div>
    </div>
</div>',
        ],
        'yardim-merkezi' => [
            'title'    => 'Yardım Merkezi & Kurulum Videoları',
            'template' => 'page-help-center.php',
            'content'  => '<div class="prose-alert prose-alert-info"><span class="alert-icon">🛠️</span><div class="alert-body"><strong>Emdief Home Yardım Merkezi:</strong> Montessori mobilyalarınızın 5 dakikalık pratik montaj videoları, kullanım rehberleri ve canlı destek alanı.</div></div>',
        ],
    ];
}

/**
 * Kurumsal Sayfaların Otomatik Oluşturulması ve Eşleştirilmesi
 */
function mis360_setup_corporate_pages() {
    global $wpdb;
    // Veritabanındaki tüm içeriklerde eski unvanı doğrudan SQL ile anında güncelle
    $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, 'Emdief Home (MİS360 Teknoloji)', 'Emdief Home (Orhan TEBER)') WHERE post_content LIKE '%MİS360 Teknoloji%' OR post_content LIKE '%MIS360 Teknoloji%'");
    $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, 'MİS360 Teknoloji', 'Orhan TEBER') WHERE post_content LIKE '%MİS360 Teknoloji%' OR post_content LIKE '%MIS360 Teknoloji%'");

    $pages = mis360_get_corporate_pages_data();

    foreach ($pages as $slug => $page_data) {
        $existing = get_page_by_path($slug, OBJECT, 'page');
        if (!$existing) {
            $existing = get_page_by_title($page_data['title'], OBJECT, 'page');
        }

        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'     => $page_data['title'],
                'post_name'      => $slug,
                'post_content'   => $page_data['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                $target_template = isset($page_data['template']) ? $page_data['template'] : 'page-corporate.php';
                update_post_meta($page_id, '_wp_page_template', $target_template);
            }
        } else {
            // Eğer içerikte eski unvan, eksik adres veya eski Kredi Kartı ibaresi varsa ya da Banka Havalesi / geniş Montessori ürünleri eksikse güncelle
            $needs_refresh = false;
            if (!empty($existing->post_content)) {
                if (strpos($existing->post_content, 'MİS360 Teknoloji') !== false || strpos($existing->post_content, 'MIS360 Teknoloji') !== false) {
                    $needs_refresh = true;
                }
                if (strpos($existing->post_content, 'Orhan TEBER') === false && in_array($slug, ['mesafeli-satis-sozlesmesi', 'gizlilik-ve-kvkk'], true)) {
                    $needs_refresh = true;
                }
                if (strpos($existing->post_content, 'Mobilya Kent') === false && in_array($slug, ['mesafeli-satis-sozlesmesi', 'iletisim', 'gizlilik-ve-kvkk'], true)) {
                    $needs_refresh = true;
                }
                if (strpos($existing->post_content, 'Kredi Kartı') !== false && in_array($slug, ['gizlilik-ve-kvkk', 'mesafeli-satis-sozlesmesi', 'teslimat-ve-iade'], true)) {
                    $needs_refresh = true;
                }
                if (strpos($existing->post_content, 'Banka Havalesi') === false && in_array($slug, ['gizlilik-ve-kvkk', 'mesafeli-satis-sozlesmesi', 'teslimat-ve-iade'], true)) {
                    $needs_refresh = true;
                }
                if (strpos($existing->post_content, 'Eğitici Ahşap Çocuk Oyuncakları') === false && in_array($slug, ['hakkimizda', 'mesafeli-satis-sozlesmesi'], true)) {
                    $needs_refresh = true;
                }
            }

            if ($needs_refresh) {
                wp_update_post([
                    'ID'           => $existing->ID,
                    'post_content' => $page_data['content'],
                ]);
            }

            // Mevcut sayfa şablonunu kontrol et
            $current_template = get_post_meta($existing->ID, '_wp_page_template', true);
            if (empty($current_template) || $current_template === 'default') {
                update_post_meta($existing->ID, '_wp_page_template', 'page-corporate.php');
            }
        }
    }
}
add_action('after_switch_theme', 'mis360_setup_corporate_pages');
add_action('admin_init', 'mis360_setup_corporate_pages');

/**
 * İlk sayfa yüklemesinde (ön yüz veya arka yüz) veritabanı senkronizasyonunu tetikle
 */
function mis360_maybe_sync_corporate_pages() {
    $version_key = 'mis360_corporate_v151_synced';
    if (!get_option($version_key) || is_admin()) {
        mis360_setup_corporate_pages();
        update_option($version_key, 1);
    }
}
add_action('init', 'mis360_maybe_sync_corporate_pages');

/**
 * Sayfa Görüntülenirken Unvan ve İçerik Güvencesi Filtresi
 */
function mis360_clean_corporate_content($content) {
    if (is_singular() || is_page()) {
        $content = str_replace(
            [
                'Emdief Home (MİS360 Teknoloji)',
                'Emdief Home (MIS360 Teknoloji)',
                'MİS360 Teknoloji',
                'MIS360 Teknoloji',
            ],
            [
                'Emdief Home (Orhan TEBER)',
                'Emdief Home (Orhan TEBER)',
                'Orhan TEBER',
                'Orhan TEBER',
            ],
            $content
        );
    }
    return $content;
}
add_filter('the_content', 'mis360_clean_corporate_content', 1);

/**
 * Sayfa Görüntülenirken Kurumsal Şablonu Otomatik Filtrele
 */
function mis360_corporate_template_include($template) {
    if (is_page()) {
        global $post;
        if ($post && in_array($post->post_name, ['yardim-merkezi', 'help-center', 'kurulum-videolari'], true)) {
            $help_template = locate_template(['page-help-center.php']);
            if (!empty($help_template)) {
                return $help_template;
            }
        }

        $corp_slugs = [
            'cerez-politikasi',
            'gizlilik-ve-kvkk',
            'mesafeli-satis-sozlesmesi',
            'teslimat-ve-iade',
            'hakkimizda',
            'iletisim',
            'about-us',
            'teslimat-iade',
            'kvkk'
        ];
        if ($post && in_array($post->post_name, $corp_slugs, true)) {
            $corp_template = locate_template(['page-corporate.php']);
            if (!empty($corp_template)) {
                return $corp_template;
            }
        }
    }
    return $template;
}
add_filter('template_include', 'mis360_corporate_template_include', 99);
