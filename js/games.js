    // Local Storage'dan oyun listesini ve tema tercihini yükleme. Json kullanımını w3schoolstan öğrendim. çünkü istediğim 
    // kullanıcının girdiği verilerin sitede kalıcı olmasını sağlamaktı burayı internetten ve yapay zekadan yararlanarak yaptım.
    var hafizadakiOyunlar = JSON.parse(localStorage.getItem("games")) || [];
    //|| [] (Mantıksal veya): Eğer hafızada henüz hiçbir şey kayıtlı değilse
    //  (yani siteye ilk kez giriliyorsa), kodun hata verip çökmesini engeller ve değişkeni boş bir dizi olarak başlatır.

    // Tema ayarlama
    // 1. Hafızadan "tema" bilgisini al, yoksa varsayılanı "dark" yap
    function loadtema() {
        var kayitliTema = localStorage.getItem("tema") || "dark";
        if (kayitliTema === "dark") { // Varsayılanı dark-mode olarak ayarla
            document.body.classList.add("dark-mode");
        }
    }
    loadtema(); 

    // Tema değiştirme butonu
    var temaButonu = document.getElementById("Tema-toggle");
    if (temaButonu) {
        temaButonu.onclick = function() {
            document.body.classList.toggle("dark-mode");
            
            var suAnkiTema = "light";
            if (document.body.classList.contains("dark-mode")) {
                suAnkiTema = "dark";
            }
            localStorage.setItem("tema", suAnkiTema);
        };
    }

    // index.html kısmı
    var addToListButtons = document.querySelectorAll(".add-to-list");
    //Sayfadaki tüm "Listeye Ekle" butonlarını bulur ve bir liste içine koyar.
    //buradaki döngüyü yazarken yapay zekadan yardım aldım.

    for (var i = 0; i < addToListButtons.length; i++) 
        //Sayfada kaç tane buton varsa hepsine tek tek gidip aşağıdaki işlemleri tanımlar.
    {
        addToListButtons[i].onclick = function(event)
        {
            event.preventDefault(); 
            
            var gameName = this.getAttribute("data-game-name");
            //Hangi butona basıldıysa, o butonun içindeki oyun ismini (data-game-name) alır.
            
            var gameItem = {//Oyunun bilgilerini içeren bir obje oluşturur.
                name: gameName,
                type: "Popüler", 
                status: "Oynanacak",
                playTime: "", 
                userScore: "",
                comment: ""
            };
            
            // Yeni oyunu listenin en başına ekler. Böylece arşivde en üstte görünür. allta olması için push
            hafizadakiOyunlar.unshift(gameItem);
            localStorage.setItem("games", JSON.stringify(hafizadakiOyunlar));
            //Güncellenmiş listeyi tarayıcının kalıcı hafızasına (localStorage) kaydeder. Böylece sayfa kapansa da veriler silinmez.
            
            alert(gameName + " listenize eklendi!");//kullanıcıya alert göndermek için yukarıda
        };
    }

    // add-game.html kısmı
    var addGameForm = document.getElementById("gameForm");

    if (addGameForm) {
        addGameForm.onsubmit = function (event) //: Kullanıcı "Ekle" butonuna bastığı an yapılacak işlemleri başlatır.
        { 
            event.preventDefault();//Formun normalde yaptığı sayfa yenileme hareketini durdurur. 
            // Bu sayede sayfa yenilenmeden verileri kaydedebiliriz
            
            var gameNameInput = document.getElementById("gameName");
            var gameTypeInput = document.getElementById("gameType");
            var playedStatusElement = document.querySelector('input[name="played"]:checked');
            var playTimeInput = document.getElementById("playTime"); 

            var gameName = gameNameInput.value.trim();
            var gameType = gameTypeInput.value;
            var playTime = playTimeInput.value;

            if (gameName === "" || gameType === "" || !playedStatusElement) {
                alert("Lütfen Oyun Adı, Türü ve Oynama/İzleme süresi alanlarını doldurun.");
                return; 
            }
            if (gameName.length < 2) {
                alert("Oyun adı en az 2 karakter olmalıdır.");
                return;
}
            var gameItem = {
                name: gameName,
                type: gameType,
                status: playedStatusElement.value,
                playTime: playTime, 
                userScore: "",
                comment: ""
            };
        
            hafizadakiOyunlar.unshift(gameItem);
            localStorage.setItem("games", JSON.stringify(hafizadakiOyunlar));

            alert("Oyun listenize eklendi!");
            addGameForm.reset(); 
        };
    }

    //games.html kısmı
    var listarananoyunlar = document.getElementById("gameList");

    if (listarananoyunlar) {
        if (hafizadakiOyunlar.length === 0) {
            listarananoyunlar.innerHTML = "<p>Listenizde henüz oyun yok.</p>"; 
            //: Eğer hafızada hiç oyun yoksa ekrana yazar
        } else {
            for (var j = 0; j < hafizadakiOyunlar.length; j++) {
                var currentGame = hafizadakiOyunlar[j];
                
                var li = document.createElement("li"); //Her oyun için yeni bir liste öğesi  oluşturur.
                li.innerHTML =//<li> etiketinin içine oyunun adını, puan, yorum  ve Kaydet/Sil yerleştirmek için
                    "<b>" + currentGame.name + "</b> (" + currentGame.type + ")<br>" +
                    "Puan: <input type='number' min='0' max='10' id='score_" + j + "' value='" + (currentGame.userScore || "") + "'>" +
                    " Yorum: <textarea id='comment_" + j + "'>" + (currentGame.comment || "") + "</textarea>" +
                    "<button onclick='kaydetKismi(" + j + ")' class='yes-buton'>Kaydet</button>" +
                    "<button onclick='oyunKaldir(" + j + ")' class='no-buton'>Sil</button>";
                    
                listarananoyunlar.appendChild(li); //ana listeye eklemek için kullandım
            }
        }
    }

    function kaydetKismi(index) {
        var scoreValue = document.getElementById("score_" + index).value;
        var commentValue = document.getElementById("comment_" + index).value;

        hafizadakiOyunlar[index].userScore = scoreValue;
        hafizadakiOyunlar[index].comment = commentValue; 

        localStorage.setItem("games", JSON.stringify(hafizadakiOyunlar));
        alert("Kayıt Başarılı!");
    }//buraları kaydetme ve silme işlemleri için yazdım.

    function oyunKaldir(index) {
        if (confirm("Silmek istediğinize emin misiniz?")) {
            hafizadakiOyunlar.splice(index, 1);//Seçilen oyunu listeden tamamen çıkartmak için
            localStorage.setItem("games", JSON.stringify(hafizadakiOyunlar));
            location.reload(); 
        }
    }

    //archive.html kısmı
    //burada yapay zekadan yararlandığım kısımlar oldu
    var archiveArea = document.getElementById("archiveArea");

    if (archiveArea) {
        var cardsHtml = "";//Oyun kartlarını oluşturup içine biriktireceğimiz boş bir metin kutusu hazırlar
        for (var k = 0; k < hafizadakiOyunlar.length; k++) {
            var oyun = hafizadakiOyunlar[k]; 
            

            cardsHtml += //Her bir oyun için bir kart oluşturur. 
            // Bu kartın içinde oyunun adı, türü, durumu, puanı ve yorumu yer alır
                "<div class='card'>" +
                "<h3>" + oyun.name + "</h3>" +
                "<p><b>Tür:</b> " + oyun.type + "</p>" +
                "<p><b>Durum:</b> " + oyun.status + "</p>" +
                "<p><b>Puan:</b> " + (oyun.userScore || "Puanlanmadı") + "/10</p>" +
                "<p><b>Yorum:</b> " + (oyun.comment || "Yorum yok") + "</p>" +
                "<div class='archive-actions'>" +
                    "<button onclick='oyunKaldir(" + k + ")' class='delete-buton'>Sil</button>" +
                "</div>" +
                "</div>";
        }
        
        if (hafizadakiOyunlar.length === 0) {
            archiveArea.innerHTML = "<p> Arşiviniz henüz boş. </p>";
            //Eğer liste tamamen boşsa kullanıcıya "Arşiviniz henüz boş" mesajını gösterir.
        } else {
            archiveArea.innerHTML = cardsHtml; 
            //Eğer oyun varsa hazırlanan tüm kartları tek seferde HTML içindeki archiveArea bölgesine yerleştirir
        }
    }