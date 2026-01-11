@extends('layouts.app')

@section('content')
{{-- Hero section with gradient overlay --}}
<div class="relative">
  <div style="background-image: url(https://goteborg-kraftsportklubb.web.app/img/mark-min.jpg);
  height: 500px;
  background-size: cover;
  background-position-y: center; max-height: 50vh;" class="flex items-center">
  </div>
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg tracking-tight">Göteborg Kraftsportklubb</h1>
      <div class="mt-4 flex items-center justify-center gap-3">
        <span class="h-px w-12 bg-white/50"></span>
        <p class="text-white/80 uppercase tracking-[0.3em] text-sm drop-shadow">Sedan 1933</p>
        <span class="h-px w-12 bg-white/50"></span>
      </div>
    </div>
  </div>
</div>

<div class="container mx-auto max-w-4xl px-4 py-10">
  {{-- Intro section --}}
  <div class="mb-10">
    <p class="text-xl sm:text-2xl font-medium text-gkk leading-relaxed">
      Göteborg Kraftsportklubb bildades 1933 och har idag omkring 100 medlemmar.
    </p>
    <p class="text-lg leading-relaxed text-gray-600 mt-4">
      Vi har vår egna klubb- och träningslokal hos <a class="underline hover:text-gkk transition-colors" href="https://www.friskissvettis.se/goteborg/harfinnsvi/majorna" target="_blank">Friskis & Svettis Majorna</a> i Göteborg. I föreningen finns allt från motionärer till elitaktiva på högsta nivå.
    </p>
    <div class="bg-gkk/5 border-l-4 border-gkk rounded-r-lg p-4 mb-12 mt-4">
      <p class="text-gray-700">
        Varmt välkommen att hälsa på och träna ett pass med oss!
      </p>
    </div>
  </div>

  {{-- Contact info card --}}
  <div class="bg-gradient-to-br from-gkk to-gkk-light rounded-2xl p-6 sm:p-8 shadow-lg mb-10 text-white">
    <h2 class="text-xl font-bold mb-6 flex items-center">
      <i class="fa fa-address-card mr-2"></i> Kontaktuppgifter
    </h2>
    <div class="grid gap-4 sm:grid-cols-3">
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-map-marker text-lg mt-0.5 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Adress</div>
            <div class="text-white">Karl Johansgatan 152</div>
            <div class="text-white/80 text-sm">414 51 Göteborg</div>
          </div>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-envelope text-lg mt-0.5 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Kontakt</div>
            <div class="text-white">info@gkk-styrkelyft.se</div>
          </div>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl p-4">
        <div class="flex items-start">
          <i class="fa fa-building text-lg mt-0.5 mr-3 text-white/70"></i>
          <div>
            <div class="font-semibold text-white/90 text-sm uppercase tracking-wide mb-1">Organisationsnummer</div>
            <div class="text-white">802456-7672</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Payment section --}}
  <div class="bg-white rounded-2xl p-6 sm:p-8 mb-10 border-2 border-gkk/20 shadow-md">
    <h2 class="text-xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-credit-card mr-2"></i> Betalning
    </h2>
    <div class="grid gap-6 sm:grid-cols-2">
      {{-- Swish --}}
      <div class="bg-gray-50 rounded-xl p-5">
        <div class="text-sm text-gray-500 uppercase tracking-wide mb-2 font-semibold">Swish</div>
        <div class="flex items-center gap-4">
          <img width="100" class="rounded-lg" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fqr.png?alt=media&token=1692752d-65b1-4723-b36e-2f9065ade3d5">
          <div>
            <p class="text-2xl font-bold text-gkk mb-2">123-581 34 56</p>
            <a href="https://app.swish.nu/1/p/sw/?sw=1235813456" class="inline-flex items-center px-3 py-1.5 mr-4 bg-gkk text-white font-medium rounded-lg shadow hover:bg-gkk-light transition-all duration-200">
              <i class="fa fa-mobile mr-1.5"></i>
              Öppna Swish
            </a>
          </div>
        </div>
      </div>
      {{-- Bankgiro --}}
      <div class="bg-gray-50 rounded-xl p-5 flex flex-col justify-center">
        <div class="text-sm text-gray-500 uppercase tracking-wide mb-2 font-semibold">Bankgiro</div>
        <p class="text-2xl font-bold text-gkk">846-0891</p>
      </div>
    </div>
  </div>

  {{-- Board section --}}
  <div class="mb-12">
    <h2 class="text-2xl font-bold text-gkk mb-8 text-center">Förtroendevalda 2025</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://goteborg-kraftsportklubb.web.app/img/stryrelsen_carl-1.jpeg" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Carl Öberg</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Ordförande</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fjenny.png?alt=media&token=8c1c612e-75ca-4eac-849e-e5ef0b060f67" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Jenny Karlsson</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Kassör</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://avatars.githubusercontent.com/u/25909128?v=4" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Martin Sandström Krisell</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Sekreterare</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://goteborg-kraftsportklubb.web.app/img/styrelsen_albin.jpg" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Albin Björkman</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Ledamot</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full overflow-hidden mb-3">
            <img class="w-full h-full object-cover" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FPhilip.png?alt=media&token=5018ed03-a106-4999-95da-d5db596e4e8a" alt="">
          </div>
          <h3 class="text-gray-900 text-sm font-medium">Philip Larsson</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Ledamot</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full overflow-hidden mb-3">
            <img class="w-full h-full object-cover" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FIdaV.png?alt=media&token=a779389c-ccbc-4d4a-aece-14a8a9312431" alt="">
          </div>
          <h3 class="text-gray-900 text-sm font-medium">Ida Vingesand</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Ledamot</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2FIda.png?alt=media&token=371aa963-19a4-494a-9146-3fe63a0562ed" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Ida Anemyr</h3>
          <p class="text-gkk text-xs font-semibold mt-1">Ledamot</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full overflow-hidden mb-3">
            <img class="w-full h-full object-cover" src="https://media.licdn.com/dms/image/v2/D4D03AQGkPTFT6yS5oA/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1709116948089?e=2147483647&v=beta&t=PMk6RPkdleWR8xAFD9vYj2IdWNFPQF1CD6P1k-h_zjk" alt="">
          </div>
          <h3 class="text-gray-900 text-sm font-medium">Sebastian Bratland</h3>
          <p class="text-gray-500 text-xs mt-1">Suppleant</p>
        </div>
      </div>
      <div class="group">
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 hover:shadow-lg">
          <img class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 mx-auto rounded-full object-cover mb-3" src="https://firebasestorage.googleapis.com/v0/b/goteborg-kraftsportklubb.appspot.com/o/static%2Fmaximus2.png?alt=media&token=0e728e58-5db1-42b5-99c8-6ef86a53ff8e" alt="">
          <h3 class="text-gray-900 text-sm font-medium">Maximus Sörnes Andersson</h3>
          <p class="text-gray-500 text-xs mt-1">Suppleant</p>
        </div>
      </div>
    </div>
  </div>

  {{-- History section --}}
  <div class="mb-10">
    <h2 class="text-2xl font-bold text-gkk mb-6 flex items-center">
      <i class="fa fa-history mr-2"></i> Historia
    </h2>
    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg p-4 mb-6">
      <p class="text-amber-800 text-sm">
        <i class="fa fa-info-circle mr-1"></i>
        Denna text är tagen i sin helhet från vår tidigare hemsida. Beslut om den ska ligga kvar och i vilken form är inte taget.
      </p>
    </div>
    <div class="prose prose-lg max-w-none text-gray-600 space-y-4">
      <p>Göteborgs Kraftsportklubb startade 1933 med tyngdlyftning på programmet d.v.s stöt, press och ryck. I slutet av 70-talet startade numera framlidne Åke Bohlin upp en styrkelyftssektion d.v.s Knäböjning, Bänkpress och Marklyft. Man höll till i Tuve och där fostrades under Åkes hårda drillning många fina lyftare. Stefan Nentis var den största stjärnan under 70 och 80-talen med många ädla medaljer på VM, EM, SM. Även numera framlidne Zlatko Radojkovic var med i yppersta eliten. Under den här perioden har det passerat många lyftare som har hållit god SM-klass. I slutet av 80-talet anslöt sig bl.a Dennis Andersson, Bertil Sundvall och de har under 90-talet hållit god SM-klass.Vi får inte glömma den starke dragkamparen Erik Johansson som även han hade en del internationella uppdrag med viss framgång.</p>

      <p>I början av 90-talet anslöt sig så Sveriges bäste lyftare genom tiderna( i skrivande stund )Kenneth Mattsson till klubben. Kenneth har tidigare tävlat för andra göteborgsklubbar och även gjort avstickare utanför stadsgränsen. Började redan under tidigt 70-tal slipa formen för framtida uppdrag. Började nå lite god SM-status i mitten på 70-talet och började lukta lite internationell krutrök under senare halvan av 70-talet.Även om Kenneth inte tillhörde klubben under 80-talet så lade han säkert lite av grunden till sina framgångar tillsammans med bl a Stefan Nentis där man peppade varandra och tränade hårt. Detta ledde till 2 guldmedaljer i Styrkelyft 82-83.Stefan fick guld 82. Även andra ädla medaljer har erövrats.</p>

      <p>Under andra halvan av 90-talet lämnade styrkelyften tyngdlyftningsfamiljen och bildade Svenska Styrkelyfts förbundet. Inte långt senare startade en ny tävlingsform iform av bänkpress helt bortkopplad från styrkelyftens bänk och med egna rekord.Även här har Kenneth varit den stora stjärnan med flera ädla medaljer på stora mästerskap.Även Lars Ahlqvist har visat god kapacitet i denna gren och går stadigt framåt genom hård och målmedveten träning.Lars har under många år tillhört klubben och har även varit en duktig styrkelyftare med SM-deltagande.</p>

      <h3 class="text-xl font-bold text-gkk mt-8 mb-4">Klubbens pionjärer</h3>
      <p class="text-sm text-gray-500 italic mb-4">Ur Rolan Jerneryds bok "Hur idrotten kom till stan"</p>

      <p>Alltsedan urminnes tider och genom seklerna har alltid styrkan beundrats. I sägen och myt förtäljes om kraftprestationer som ofta har övernaturliga inslag. I mitten av 1800-talet fick tyngdlyftningen ökad popularitet i Tyskland och Frankrike. År 1860 bildades i Österrike "Erster Wiener Athleten Club". I Sverige bildades år 1887 den första specialklubben för tyngdlyftning, Östermalms Atletklubb.</p>

      <p>I Göteborg började tävlingar i tyngdlyftning på 1880-talet inom de då nybildade idrottsklubbarna. Sålunda hade Idrottssällskapet LS stiftats år 1883 och ÖIS 1887, båda atletik på sina program. Vid denna tid räknades tyngdlyftning till de fria idrotterna och ÖIS hade från sina första tävlingar år 1888 och under många år framåt viktlyftning som en gren vid sina årliga friidrottstävlingar. I programmet kunde förekomma "vigtlyftning, sjelfvalda rörelser med vigter från 17-85 kg", "uppkastning av tyngsta vigt" eller "pressning av 70 kg kulstång".</p>

      <p>Vid Svenska Idrottsförbundets första SM-tävlingar i Helsingborg fanns även tyngdlyftning på programmet. Från detta första mästerskap 1896 och fram till och med år 1913 ägde tävlingarna rum utan viktklassindelning. I viktlyftning, press, segrade vid första SM-tävlingen CL Wikström, Stockholms Atletförening som pressade 70kg 13 gånger och i uppkastning av tyngsta vikt vann Erik Lindström, Stockholms Allmänna Atletklubb genom att stöta 125kg. När mästerskapen 1897 ägde rum i Göteborg blev det Göteborgsplaceringar i prislistan. I viktlyftning där Erik Lindström SAK segrade med 12 pressningar belade Albin Tingdahl Göteborgs Atletklubb tredje plats med 10 gånger press av 70kg. I uppkastning av tyngsta vikt vann Erik Lindström Stockhols Allmänna Atletklubb genom att stöta 125kg.</p>

      <p>Tyngdlyftning och brottning tillhörde Svenska Idrottsförbundet från dess start 1896 och fram till 1909. Detta år bildades Svenska Atletikförbundet som fram till 1920 då Svenska Brottningsförbundet bildades administrerade de båda kraftsporterna. Tyngdlyftningen sorterade därefter i två år under det kvarvarande Atletikförbundet. Den 26 mars 1922 bildades Svenska Tyngdlyftningsförbundet. Jubileumsåret 1923 fick Göteborg i samband med Svenska Idrottsspelen uppleva det största tyngdlyftningsevanemang som dittils ägt rum i Sverige. Praktiskt taget hela den dåvarande världseliten var församlad på Cirkus vid dessa tävlingar och de följdes med stort intresse av Göteborgspubliken. I fyra av de fem viktklasserna hade segraren en världsmästartitel. Estländarna erövrade lagpriset och klassegrarna var följande: Fjädervikt Andreas Stadler Österrike, 230kg, lättvikt Alfred Neuland Estland 250kg, mellantungvikt Karl Freiberger Österrike 272,5kg, lätt tungvikt Henric Lang Tyskland 265kg, tungvikt Harald Tammer Estland 265kg.</p>

      <p>Tyngdlyftningen bedrivs vid denna tid i brottarklubbarna ÖIS och GAK som styrketräning för brottarna. Som en "vid sidan om" gren nådde man inga mästerskap i en så svårlärd disciplin som tyngdlyftning. Det saknades en specialklubb. I början av 30-talet väcktes intresset för tyngdlyftningen åter till liv. Frisksportarrörelsen som utvecklats kraftigt vid denna tidpunkt propagerade för olika slag av kraftsporter. Tidningen Frisksport, som började utgivas i Göteborg 1932 av Edvin Ahlqvist och Agne Swalander gav impulsen. Ahlqvist och Swalander arrangerade även i propagandasyfte kraftsportgalor på Cirkus med brottning, boxning, dragkamp, armbrytning och fingerkrok. Även uppvisning i tyngdlyftning förekom. Det var därför en naturlig utveckling att de båda kraftsportarrangörerna som låg bakom det sammanträde som ägde rum i februari 1933 på Brobergs Sportgymnasium där avsikten var att bilda en tyngdlyftningsklubb.</p>

      <p>Mötet var talrikt besökt och man beslöt enhälligt att en klubb skulle bildas. Den stiftades strax efter, den 7 mars 1933 och fick som sin förste ordförande Nils Lidman. Namnet på klubben blev Göteborg Kraftsportklubb.</p>

      <p>Den första provisoriska träningsplatsen fick man sommaren 1933 inom Vallens område söder om idrottsplatsen. Medelst medlemsavgifter och tiggarlistor lyckades klubben på avbetalning anskaffa lyftredskap. Första vintern höll man till i ett blåsigt magasin på Oscarsgatan. Klubben startade under synnerligen karga förhållanden men en entusiasmen var stor bland medlemmarna.Genom propagandauppvisningar i och utanför Göteborg kunde man även tillföra klubbkassan pengar för verksamheten.En av stiftarna som mycket snart kom med i klubbstyrelsen var Sven Nilsson (bilden). Han hade redan tidigare med framgång tävlat i brottning för ÖIS där han under några år var distriktmästare i tungvikt.Nilsson blev klubbens entusiastiske eldsjäl och var under åren 1933-45 sekreterare, kassör och ordförande. Under de första årens DM-tävlingar 1934-37 tog han fyra mästerskap och 1936 blev han svensk juniormästare i tungvikt. Men det administriva arbetet tog mer och mer överhand för Nilsson både i klubben och på riksplanet. Det gjorde att han fick lämna över tävlingslyftningen till den nya generationen som startat med grenen som första idrott och fått lära sig den svåra tekniken från grunden.</p>

      <p>Året efter det att Kraftklubben hade bildats tillkom Mölndals Atletklubb. Detta betydde att man fick stimulerande lokalkonkurens. Man kunde nu börja tävla om distiktmästerskapstitlarna. Dessa tävlingar gällde i första hand tvåarmsgrenarna, men från 1936-45 fanns även enarmsgrenarna på programmet. Resultaten var väl till att börja med inte jämnförbara med den svenska eliten, men man närmade sig steg för steg. Tränare utifrån som Svend Olsen Danmark och Olympiamästaren 1936 Joseph Manger Tyskland samt svenska mästare som Ove Lövdal Västerås, "Hyresgäst" Andersson Stockholm, Sture Karlsson och Reinhold Pettersson Kalmar, instruerade göteborgarna genom åren i den tekniskt svåra tyngdlyftningen. Intresset var stort bland göteborgsatleterna och göteborgsrekorden blev allt bättre. Kraftsportklubben kunde snart mönstra ett lag som med framgång mötte utombys lyftare.</p>

      <p>Den första framgången nådde man när Landskrona besegrades i en stadsmatch 1936. Tyngdlyftningsförbundet placerade redan 1936 ett junior-SM i Göteborg. Tävlingarna i Ex-huset blev fin propaganda för sporten. Klubben fick en svensk mästare genom Sven Nilsson och det kunde ha blivit ytterligare ett mästerskap om inte Gunnar Wikström vägt över i fjädervikt.Vid kraftklubbens femårsjubileum 1938 fick man äran att arrangera SM i tvåarmslyften.Folkteatern i Lorensbergsparken fick tjänstgöra som arena.Året 1939 blev Göteborg åter platsen för junior-SM i tvåarmslyften.Något mästerskap stannade dock inte i Göteborg den hät gången.Erland Lövdahl som var favorit ledde efter press och ryck men blev trots detta bara fyra.</p>

      <p>Ytterligare ett junior-SM fick göteborgarna ta hand om 1946. Den stora livaktigheten inom kraftsporten förde med sig allt fler utövare och i slutet av 30-talet var göteborgsklubben Sveriges största.Det nyinstiftade tyngdlyftarmärket var en fin propaganda och göteborgarna toppade i flera år listan över mest avlagda prov i Sverige.Lovande ungdomar visade snart mästartakter.John Boström var en av de första som började bolla med rekorden.Under 30-talet tog han 7 DM i lättvikt och mellanvikt och fram till 1946 höll han göteborgsrekordet i lättvikt i press av stång med två armar 80kg (1934) och stöt av stång med två armar 110kg (1937).Boström erövrade 1938 ett svenskt junior-SM i lättvikt .I tungvikt dominerade Erland Lövdahl under senare delen av 30-talet och tog hand om 14 DM-titlar fram till 1949,men under 40-talet fick han jämnbördig konkurrens av klubbkamraten Tor Lindberg som fram till 1950 tog 13 titlar.</p>

      <p>Det var emellertid i de lättare klasserna som GKK upplevde de största framgångarna.Först var det Arne Wallander i fjädervikt som satte svenska rekord i enarmsryck 60kg,i enarmsstöt 67,5kg och sammanlagt 225kg.Även i tvåarmsstöt med 110kg och sammanlagt 270kg satte han svenskt rekord .Han blev svensk juniormästare i fjädervikt 1942 med 222,5kg,men något svenskt seniormästerskap lyckades Wallander dock inte hemföra trots sina rekord.Arne Östergren som efterträdde Wallander som fjäderviktsmästaren i Göteborg blev den som erövrade de första senior-SM-titlarna till Göteborg sedan 1910.Han besegrade svenska eliten i bantamvikt och tog 1946 titeln i enarmslyft med 227,5kg. Östergren upprepade segern 1947 då han vann på 220kg.Samma år tog han även SM-titeln i tvåarmslyft i bantamvikt med resultatet 257,5kg.Roland Helgesson i lättvikt var även under senare delen av 40-talet en framstående lyftare som åren 1944-50 lade beslag på 13 DM-titlar och under 1950 innehade samtliga 8 distriktsrekord i lättvikt.</p>

      <p>Tyngdlyftningen utvecklade en livlig aktivitet på 40-talet.För att öka konkurrensen på lokalplanet beslöt ÖIS att även starta en tyngdlyftningssektion 1945 och lyckades värva över flera av GKK:s bästa lyftare,bland dem Arne Wallander.Även Polisens IF tävlade under några år.Tyngdlyftningen blev aldrig den stora publiksporten och därför hade kraftsportarna ständiga svårigheter med ekonomin.Kraftsportklubbens problem med ständiga byten av träningslokaler ger en bild av detta.Det gällde att få billiga och därmed också tillfälliga träningsutrymmen.Från 1933 fram till 1950 hade klubben tolv olika lokaler.Förhoppningen att få en egen lokal som kunde disponeras helt efter medlemmarnas varierande önskemål kunde tyvärr aldrig infrias under dessa år.Fördelningen av distriktmästerskap under åren 1934-50 var följande:Seniorer GKK 102 titlar, MAK 27 titlar och ÖIS (1945-46)8 titlar.Juniorer:GKK 36 titlar,MAK 23 titlar,Polisens IF (1944-47) 3 titlar,ÖIS (1946) 2 titlar och Friska Viljor (1950) 1 titel.</p>

      <h3 class="text-xl font-bold text-gkk mt-8 mb-4">Historia 50- och 60-tal</h3>

      <p>Under rubriken "klubbens pionjärer" har vi avverkat tiden från det att Göteborgs Kraftsportklubb startades 1933 fram till I början av 50-talet. Under rubriken "historia" har vi sammanfattat tiden då styrkelyften gjorde entré rejält. Med hjälp av Göran Andersson som har tänkt tillbaka 40- 50 år. Göran själv mycket kapabel lyftare under mellantiden alltså efter pionjärerna och styrkelyftarna.Tack! "GKK bildades alltså 7 april 1933 I stadsdelen Gråberget.Undertecknad försöker minnas och tänker tillbaka på när jag var aktiv under tiden 56-66.Från 1956-60 så fanns det 4 tyngdlyftningsklubbar I Göteborgsömrådet.:Mölndals AK,Friska Viljor (Hisingen), Kortedala och GKK.Mossebergs AK bildades 1962 av Harry Hertzberg.</p>

      <p>Tyngdlyftning utövades nästan alltid I källare under 50-talet:</p>
      <ul class="list-disc ml-6 space-y-1">
        <li>1956: Sjömanskyrkan Stigbergstorget (källare)</li>
        <li>1956-57: Tvättinrättning Dr Westrings gata Guldheden (källare)</li>
        <li>1957-58: Valhallabadet (källare)</li>
        <li>1959-59: Slottsskogsvallen (2 omklädningsrum)</li>
        <li>1959-: Nya Ullevi (källarlokal vid löpargången)</li>
      </ul>

      <p>Sommartid bedrevs utomhusträning på Slottskogsvallen, Smittska Udden och Saltholmens nakenbad. I tyngdlyftningsklubbarna tränades det press, ryck och stöt. Styrkelyft kom till staniI slutet av 60-talet. När GKK 1959 fick en egen fast lokal vid Nya Ullevi så kom också framgångarna. På 60-talet så fanns det säkert 40-60 aktiva tyngdlyftningsklubbar i Sverige. (Hur många finns det idag?).</p>

      <h4 class="text-lg font-bold text-gkk mt-6 mb-3">GKK:s placeringar i lag-SM under 60-talet</h4>
      <div class="bg-gray-50 rounded-lg p-4 inline-block">
        <ul class="space-y-1 text-gray-700">
          <li><strong>1962:</strong> 3:a</li>
          <li><strong>1963:</strong> 2:a</li>
          <li><strong>1964:</strong> 1:a</li>
          <li><strong>1965:</strong> 1:a</li>
          <li><strong>1966:</strong> 1:a</li>
        </ul>
      </div>
      <p class="mt-4">Dessutom svenska rekordhållare I lag. Tyvärr slutade alla lagmedlemmar samtidigt och tyngdlyftningen i GKK dog sakta ut. Nytt liv fick GKK när styrkelyften tog fart på 70-talet. Lite historik: 1956-58 så fanns det 2st små gym I Göteborg, Jordhyttegatan Sandarna ( Stig Hasselgren) 6-8 st.Andra Långgatan ( Harry Hertzberg ) 6-8st. I GKK fick några byggare träna på nåder 10st. I Göteborg fanns det max 30st muskelbyggare, hur många idag? Första riktiga gymmet öppnades 1959 på Nya Ullevi (Samo Kalon Henry Bergsström). Sven Slobo öppnade som nr två ungefär 1960. Lite statistik:1958-59 så knäböjde ingen göteborgare 220kg, i marklyft så gjorde ingen 240, ingen bänkpressade 160."</p>
    </div>
  </div>
</div>
@endsection
