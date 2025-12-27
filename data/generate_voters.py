import json
import re

# Raw voter data (Name, Phone, Email)
voter_data = """
George Jordan Robertson		
Anna Pieterson		
Charles Acquah		
Kobina Brookman Amissah-Arthur		
Henry Kojo Acquah		
Patience Atta-Prempeh		
Lydia Ofei		
Christopher Anokye		
Benjamin Quansah		
Patience Bondzie		
Cecilia Wright		
David Benin		
William Bannnerman-Martin		
Benjamin Lartey		
Gloria Aidoo		
Samuel Awuah Quaye		
Esther Ntorinkansah		
Frank Atta-Peters		
Katherine Addo		
Georgina Ohene		
Albert Zanu		
Albert Awudetsey		
Mary Adubofour		
Doris Dedume-Brown		
Cynthia Yerenkyi		
Elizabeth Wellington		
Dora Dompreh		
Edwin Abakah-Williams		
Anna Portuphe		
Ebenezer Akuetteh		
George Bruce-Smith		
Daniel Kofi Bainson		
James Dzandu		
Jane Asamoah-Broni		
Janet Siaw		
Rebecca Amoah		
Rita De Here		
Joanna Harvey-Ewusi		
Franklyn Bartels		
William Arthur		
Theresa Nora Wilberforce		
Margaret Agyeman		
Rita Claudia Hagan		
Theophilus Alexander Thompson		
Susan Dougan		
Victor  Godwyll		
Vivian Nortey		
Vera Ankomah-Sey		
Michael Enchill Yawson		
Emmanuel Asante Antwi		
Amy Yankey Dzandu		
Francis Ansah		
Ben Affini		
Hertty Holm Sottie		
Augustina Clinton  Hammond		
James Adomako		
Janet Ameyaa Bram		
Augustina Erskine Essilfie		
Gladys Yerenkyi Dawson Amoah		
Lily Sekyi Nkrumah		
Francis Amprako		
Richard Ahiagbedey		
James Adomako		
Isaac Sackey		
Edward Moses		
Christina Annor		
Kweku Steel Dadzie		
Margaret Ampah		
Ruth Simpson Longdon		
Dorothy Amissah		
Emmanual Asante Antwi		
Esther Aikins		
Victor Mensah Bonsu		
Cynthia Myles Boitey		
Jeanette Dadzie		
Longdon Gifty		
Longdon Fanny		
Edith Ameen		
Mensah Isaac		
Menyah Micheal K		
Mettle Paul		
Nekundi Nelago		
Noono Margret		
Ocansey Prosper		
Oduro Isaac		
Ofori Amanfo Wilham		
Okoreeh Beatrice		
Okyere Dora		
Oppong Frank		
Osafo Mereka		
Osam Juliana		
Boison George		
Bondzie-Koomson Felix		
Brew James		
Crentsil Lucy		
Cranlson Patience		
Croffie Rose		
Dankwa Joyce		
Dontoh Alfred		
Dougan Emmanuel		
Druyeh George		
Dsane Folley		
Enninful George		
Edua Paul		
Fuseni Alhassan		
Fynn Frank		
Haisel Alice		
Haisel George		
Hassan Mohammed		
Hayford George Graham		
Ameyaw Boateng		
Amissah Comfort		
Amoah Richard		
Amoah Franci Geo		
Amui Jacquiline		
Anim-Abankwa Alex		
Arhin Alive		
Arthur Micheal Alex		
Arthur Joseph		
Ashan-Staffier Mabel		
Attom Richard		
Ayivor Leonard		
Baah Opoku		
Bart Addison Ern		
Bentum Philomina		
Biney Samuel		
Boateng John		
Okere Dora		
Lomotey Benjamin		
Adjei Edelweiss		
Adjei Douglas Kofi		
Woode Micheal		
Wilson Ruth Esi		
Vanhil Viola		
Twumasi Margaret		
Taylor Joansi		
Tofa Fiadzigbey		
Sackey Micheal		
Sackey Albert		
Reynolds Harry		
Reiner Joseph		
Quarcoo Patience		
Prah Godfrey		
Owusu Biney Alex		
Owuredu Fredrick		
Otoo Kwesi		
Boham Gifty		
Kwateng Portia		
Kutisi Henrietta		
Kusi Wilberforce		
Kumi Florence		
Kpobitey Lydia		
Koomson David		
Karikari Adjei Joan		
De-Graft Johnson J		
Insaidoo Charles		
Indongo Nanqula		
Impraim Ferdinand		
Haruna Huseini		
Abbey Theodora		
Acquah Isaac		
Acquah Joseph		
Acquah Richard		
Acquah Charles		
Acquah Matilda		
Adoboe Ferdinand		
Affore Anthony		
Afful Dina		
Afful Mercy		
Affram Charles		
Aggrey-Korsah Vic		
Agyabeng Kofi		
Aiddoo Kweku		
Aiddoo Cecilia		
Allotey Georgina		
Nancy Esme Quaigraine		esmequagrainie@gmail.com
James Ekow Paintsil		
Edna Bright Forson		
John Ato Ampiah		jaampiah@gmail.com
Samuel Aidoo		sbaidoo03@gmail.com
Samuel Bentil		bonsubandohb7763@gmail.com
Emmanuel Essampong		essampongemmanuel@gmail.com
Simeon Kwesi Essien		satadjei@gmail.com
Richard Acolatse		ekowobo@gmail.com
Bernard Bandoh		ewuramah60@gmail.com
Isaac Koomson		
Isaac Taylor		
Sarah Quaigraine		satadjei@gmail.com
Anthony Arhin		
Dorothy Akrong		
Evelyn Zomelo Ahali		
Francisca Dankwa Hall		fdankwahall20@gmail.com
Frederick Kwabena Asare		
Gaddiel Addico		
Jacob Anderson Aidoo		jaafame@yahoo.com
Kodjo Buadum Yanney		
Love Kwartefo Quartey		
Nicholas Bissue		
Queenster Asare Boateng		
Samuel Johnson		
Olivia Peprah		
Hannah Doughan		
Jacklyn Baffoe Bonney		
Joseph Attah		
Emelia Frimpong		
Gladys Ankomah		
Nii Odoye Odoye		
William Minta Jacobs		
Augustina Arhin		
Angela Bennin		
Alberta Tackie		
Joyce Dampare		
Theresa Aba Conduah		nshiraba06@yahoo.com
Cherisson Eleazer Shooter		
Efua Halm-Quagrainie		
Frank Sekyi Brew-Addaquay		
Sophia Brew		
Fred Attoh		
Doreen K Yamson		Doryamson@yahoo.com
Victor Joel Yamson		vyamson@hotmail.com
Emmanuel Donkoh		drdonkoh@gmail.com
Samuel Stanley Yamoah		stanley.yamoah@gmail.com
Regina Otoo		otoo.regina@yahoo.com
Johnson Budu-Hagan		johnsonbuduhagan@gmail.com
Dr. George Mends		nanasammends@gmail.com
Jonathan Quaynor		
Evelyn Mingle		ivemingle01@gmail.com
Yoofi Brew		yoofibrew@gmail.com
Anastaisa Adjei		
Anasatsia Boateng		
Beatrice Agyekum		
Benjamin Abbey		
Brigadier General Eric Aggrey Quashie		efaquash@gmail.com
Christine B. Awuah		xtineba@gmail.com
Daniel Kofi Stephens		
Ebenezer Awotwi		
Ebenezer Tekyi		
Ekua Brinfour		
Elizabeth Quashie		
Ernest Ghunney		ernestghunney@gmail.com
Ernestine Appiah		
Georgina Ashong		pgbernasco@gmail.com
Harriet Idun Sagoe		
Kojo Andoh Quagraine		
Mabel Thompson		thompsonmabel81@gmail.com
Millicent Oppong (Nana Buokro)		
Pearl Acolatse		
Thomas Daniel Appiah		mitcillapartners@gmail.com
Yvonne Allotey		
Tasia Boateng		tasia.boateng65@yahoo.com
Joana .E. Kwafo	+233244375308	
Neeta Owusu-Ansah	+12048902404	
Sally Brew-Hammond	+233244787495	
Cecilia Boakye		
⁠Ruby Opoku Agyemang	+447572110004	
⁠Joseph Addy	+233256 399 673	
Kweku Cobbold	+233244 718 425	
Doris Araba Aikins	+233244297433	
Efua AMISSAH	+15717222931	
Beatrice Woode	+15714128944	
Ama Morrison	+233244573787	
George Owusu	+447440068475	
Elsie Gaisie	+233244571195	
Ebenezer Ackah	+233247965641	
George Anderson		
Paa Kwesi Brew	+13015037217	
Anna Aba Arthur	+233557203628	
Sarah Bentum	+233244938413	
⁠Ebenezer Dadson	+17739608760	
Desmond Cann Arkonu	+2337305994902	
Mercy Ampah	+233553158564	
⁠Isaac Grant Morrison	+447462521244	
⁠Theresa Davis	+233242520466	
⁠Stella Hayfron	+447862712091	
⁠Nancy A. M. Turkson	+233244589899	
Faustina Adzie	+233249466224	
Carlmax Brew Aidoo	+233244022233	
William Atta Petters		
⁠Alex Eyiah	+233244470727	
⁠Samuel Acquah	+233544313965	
Adwoa Egyirba Hayfron	+447508057660 	
Emma Barnes	+447403344095	
⁠Irene Jacobs	+233244693388	
Ignatius Afenyi Eshun	+233541337184	
Ophelia Ayensu	+233242323238	
Benjamin Zajour Boampong	+233262271530	
Ophelia Ayensu		
Aggrey  Fynn		
Aggrey Otoo		
Agnes Oppong		
Akwesi Gyembibi		
Ann Simmon		
Anokyie Yeboah		
Ato Van Cliff		
Ayensu Robert		
Baaba Aggrey –Cathy		
Beatrice Efua Maclean		
Beckie Kwofie		
Benjamine Hohoubu		
Bernard Rhule		
Bertha Wryther		
Cecilia Arthur		
Chani Otuteye		
Christina Quarcoo		
Clarita Fosu		
Comfort Forkah		
Dinah Dekye		
Divine Fianu		
Douglas Tetteh		
Ebenezer Dadzie		
Efua Bentum		
Elizabeth Yankey		
Emelia Halm		
Emmanuel Mensah Brown		
Emmanuel Hammond		
Emmanuel Kofi Techie		
Emmanuel Mensah		
Essuman Francis		
Esther Asiedu		
Francis Agyirey Kwakye		
George Akrofi		
Gloria Ofori Atta		
Helena Eshun		
Helena Torgborh		
Inez Buckman		
Jemima Simmon		
Joana Hammond		
Joana Hammond Ahorlu		
Joana Kusi		
Joe Ghartey		
John Otuteye		
Catherine Amoako		
Lucy Edwards		
Leon Ahiagbedey Stallon		
Marian Acquaye		
Marie-Pearl		
Maud Banaman Martin		
Mercy Pobee		
Moses Bonsu Abban		
Naana		
Otilia Kaba		
Paul Quecoo		
Priscilla Nunoo		
Rita Rockson		
Regina Kwofie		
Rita Sarfo		
Robert Ampiah		
Rose Kuba Taylor		
Saadatu		
Samuel Asante		
Seth Damali		
Sophia Essillfi		
Sophia Graham		
Veronica Awotwi		
Veronica Baka		
William Twumasi Yeboah		
Wilson-Sey		mbaduadjetey@gmail.com 
Mavis Tetteh		
Sylvia Buckman		
Bernice Bentum		
Jacob Mensah		jemmensah@hotmail.com    
Patricia Amoah		Patricia.boachie1@gmail.com
Nana Boateng	+233202023395	
Charles Pappoe	+233542692995	
Agnes Amekugee	+233244157855	
Mina M-Bonsu		minamensahbonsu32@gmail.com
Evelyn Dodoo		naaadensuah@gmail.com 
Vida K Mensah	+233249008908	
Mary Duncan		Mduncan37@yahoo.com         
John Enninful		
Christine Dadzie	+233541954707	
Agnes Forson		kafuiforson@yahoo.com
Amoaful		
Getty Amissah		maameabakoba@gmail.com
Sarah Mensah		
Karen Arthur	+233244285930	
Christina Aboagye		
Veronica  Enninful		
Percy Duker		papaduker@gmail.com
Maame Duker		maameduker@gmail.com  
Marian Aifah		maifah.foodpharmacygh@yahoo.com 
Beatrice		beetekyi@gmail.com  
Ntsin		benjamin.ntsin@gmail.com
Grace Gbadam		Grace.andoh@yahoo.com
Helena Annor		
Ivy Arhin		
Yaw Bediako		
G J Swaniker		
Charlotte Antwi	+2332020925686	
Charles  Idun		
Efua Hayfron		
Sarah Boateng		Sarahboateng649@gmail.com
Judith Asmah Mensah		judycudjoe212@aol.com
Antoinet Larsey		
Sylvia Laryea		naaayerley1@yahoo.com
Paul Yeboah		
⁠Isaac Antwi		
Kennedy Boateng		kkboateng@hotmail.co.uk
Vivian F. S. Fiscian		vfiscian@outlook.com
George Quayson		georgequayson3@gmail.com
George Essel Addison		paakofi@hotmail.com
Anastasia Kofie		Appiahamaserwaa@yahoo.com
Thelma Sagoe		Arthur. ahoofet@gmail.com
Emmanuel Essel		emssel69@gmail.com
Elizabeth Coleman Kyere		momoiapem@gmail.com
Kweku Edusei Cann		kwekucann1@yahoo.com
Pretty Sally Mensah-Korsah		sw549703@gmail.com
Winston Okai		niikym@yahoo.co.uk
Faustina Otoo		faustinaotoo81@gmail.com
Samuel Apenteng		samapent@gmail.com
Ruby Larkai		larkairuby@gmail.com
Priscilla Appiah		priskukuaa@gmail.com
Dora Kankam Boadu		dorakaybeee@gmail.com
Richard Morkeh		rmorkeh@yahoo.co.uk
Anthony Ewusie Hackman		hackies200128@gmail.com
Nancy Evelyn Sam		Sam-dadzie@gmail.com
Patrick Ackun		ackunpatrick16@gmail.com
Jacob Brobbey		jebrob@hotmail.com
Muriel Ankomah		murielankomah@gmail.com
Ama Karikari Effah		
Rebecca Aboagye Dacosta		
Anthony Assabil		
Eric Frimpong		
John Anaafi		
Frank Adebayo Raji		
Reginald Quartey		
Ivy Quainoo		
Samuel Mensah		
Nana Otu Quartey		
Bertha Hanson Owusu		
William Hassen-Richards		
Dora Opare Addo		
Rudolf Abraham		
Albert Ashifi		
Priscilla Afful		
Christina Owoo (Aduama)		
Eunice Brown (Maame Esi/Cutty Blankson)		
Sylvia Agyare		
Philomena Mensah		
Emily Agyekum (O Asare)		
Charles Ansanyi		
Nermson		
Margaret Comnarshal		
Adwoa Boateng		
Alma Amesika		
Samuel Acquah		
Kobby Agyemang		
Regina Adofo		
Cyprian Enninful		
Ambrose Korneh		
Kofi Nsiah		
Ebo Jacobs		
Irene Quaye		
Dorothy Abeka		
Charlotte Williams		
Fred Hamilton		
Dominic Owusu Ansah		
Gifty Mavis Boateng		
Isaac Coleman		
Samuel Abban		
Kofi Poku-Agyeman Danquah		
Isiah Doe Kwao		
Samuel King Arthur		
Gina Wood		
William Osei-Poku		
Dr. Nicholas Imbeah		
Rebecca Addo		
Isaac Pobee		
Anita-Joyce Agyenim-Boateng		
Christopher Charles Oppon		
Derrick  Panford		
Abraham Amissah		
Amfo Gilbert		
Judith Fafa Semey		
Florence Coffie		
Alfred K. Gbedemah Atsonglo		
Perpetual Ferguson		
Dennis Brown Quarcoo		
Josephine Nyarkoh		
John Afful		
Albert Amponsah		
Marinna Nyamekye		
Albert Michael Acquah		
Allen Ekow Enninful		
Sylvia Bediako		
Catherine  Hammond		
Dorothy Armah		
Kweku Gillett-Spio		
Allen Ekow Enninful		
Emmanuel Arhin		
Gloria Etwire		
Felix Koomson		Fkoomson01@yahoo.com
Christina Quaicoe		christinaquaicoe720@gmail.com
Mark Howard		shepherdisgood2@gmail.com
Evans Ekow Koomson		holysecziani@gmail.com
George Ernest Bentum		bentumge72@gmail.com
Rev. Alfred Kumi Fobil		alfredkfobil@gmail.com
Stanley Abbew		sabbew@outlook.com
Vera Graham Asante		Vgraham@gctu.edu.gh
Emmanuel Anthony Wilson		Kwame3@live.co.uk
Vida Baidoo		ybaidoo@gmail.com
Maxwell Akuffo		jazmaxwell@msn.com
Joana Korkor Dugbatey		dugbakuor@gmail.com
Sabina Afriyie		shepherdisgood2@gmail.com
Annan Charles Kumi		Sabina.afriyie@yahoo.co.uk
Irene Buabin		charleskumiannan75.ug@gmail.com
Ama Amerley Nyarko		Kwame3@live.co.uk
Carol Appoh-Mensah		bentumge72@gmail.com
Evelyn Adu-Aboagye		irenebuabin45@gmail.com
Wisdom Anderson Kordorwu (Togbi Tsidi Iii)		alfredkfobil@gmail.com
Peptual Quarshie		sabbew@outlook.com
Josephine Acheampong		Amerley.nyarko@gmail.com
Kwamina Ansah-Serk		Carolmensah@yahoo.de
Simon Otuteye		evelynaduaboagye74@gmail.com
Ransford Oduro Amoah		Wissyanderson1974@gmail.com
Nii Adjei		mutualq@hotmail.com
Felix Kwofie		Josieeach@aol.com
Cassandra Akornyo		serksquare@gmail.com
Justina Christian		serksquare@gmail.com
Gladys Osei Owusu		gosei-owusu@gcb.com.gh 
Theo  Osei Owusu		serksquare@gmail.com
Odenho Nana Yaw Aseidu		theosewus@gmail.com.gh
Edward Gyebi		Nanayasiedu@gmail.com
Benjamin Nii Aryeetey Addo-Quaye		 edantwi@yahoo.com.  
Lucy Danquah		Sound.wabenzy@yahoo.com
Kojo Donkor-		danquahadwoa72@gmail.com
Doris Nuamah Hagan		ddiggy73@yahoo.com
Kennedy Adjei		nuamahagandoris@yahoo.com
Edward Lmbeah		kidy67@gmail.com
Sandra Agonyo		eddidenky@yahoo.com
Cynthia Annan		Sandraagonyo44@gmail.com
Lilly Nuamah		cynthannan@yahoo.com
Johnson Siaw		lnuamah2013@gmsil.com
Amos Mensah		kensiaw3@gmail.com
Serk Ansah		pagastymens@gmail.com
Patrick Kwasi Yorke		qusis550@gmail.com
Nathaniel Abbeyquaye		natabbeyquaye@gmail.com 
Peace Kamassah		peacekamassah16@gmail.com
Cassandra Akornyo		Cassandraokuada @gmail.com
Felix Koomson		Fkoomson01@yahoo.com
Vida Baidoo		christinaquaicoe720@gmail.com
Sabina Afriyie		shepherdisgood2@gmail.com
Carol Appoh-Mensah		holysecziani@gmail.com
Joyce Osei-Appaw		bentumge72@gmail.com
Lilly Nuamah		alfredkfobil@gmail.com
Felix Kwofie		sabbew@outlook.com
Kojo Donkor		Vgraham@gctu.edu.gh
Cassandra Akworkor Agonyo		Kwame3@live.co.uk
Elvis Ghansah		ybaidoo@gmail.com
Catherine Osei-Bonsu		bentumge72@gmail.com
Joseph Bempah		jazmaxwell@msn.com
Paul Afriyie Owusu		dugbakuor@gmail.com
Christopher Ato Sackey		shepherdisgood2@gmail.com
Joyce Anima		 Sabina.afriyie@yahoo.co.uk
Doris Owusu		charleskumiannan75.ug@gmail.com
Sandra Akweley Agonyo		Kwame3@live.co.uk
Zipporah Agbemeyale		bentumge72@gmail.com
Abner Newlove Mensah		irenebuabin45@gmail.com
Emmanuel Adu Bruce		sabbew@outlook.com
Albert Tawiah Kobina Esia - Donkoh		Amerley.nyarko@gmail.com
Alex Harvey		Carolmensah@yahoo.de
Anastasia Tackie		evelynaduaboagye74@gmail.com
Andrews Alipoe		Wissyanderson1974@gmail.com
Anisat Simmons		mutualq@hotmail.com
Athanasius Mensah		Josieeach@aol.com
Benjamin Addoquaye		ransamoah@hotmail.com 
Bona Boadi		sotuteye@gmail.com
Caro Appau		Kofibona@gmail.com
Comfort Asare		
Clement Appiah		
Cynthia Appiah Baiden		
Daniel Adjei		
David Ofosuware		
David Owusu		
David Saade		
Dd Johnson		
Doreen Dodoo		
Doris Baidu		
Doris Appiah-Boadu		
Dulce Obeng		
Edmund Baffour		
Emelia Baah		
Emelia Osei		
Emma Aikins		
Emma Akrofi		
Esther Dodoo		
Eunice Kitson		
Evans Gaisie		
Fanny Amissah		
Francis Owusu		
Frank Eshun		
Fred Dodoo		
Frederick Aziabor		
Gertrude Tei - Yeboah		
Gifty Owusu Ansah		
Godson Tetteh		
Gomez Dzotope		
Henry Halm Luthrouht		
Ida Jeng		
Joseph Clinton		
Josephine Dadzie		
Joyce Asubonteng		
Juliet Stump		
Kingsley Morrison		
Ewusi Kofi Cann		
Kwesi Owusu Boateng		
Leslie Martins		
Louis Mensah		
Lucy Forson		
Macdonald Okai		
Maria Adams		
Monica Acquah		
Moses Mensah		
Nancy Sadungu		
Patience Quaye		
Percy Lamptey		
Percy Brown		
Perpertual Bortey		
Perry Boakye		
Philip Frietas		
Ramsey Buckman		
Samilia Mintah		
Selina Amoah - Dafour		
Richard Andorful		
Seth Darkwa		
Stella Nsia - Tabiri		
Thomas Nyarko		
Veronica Tagoe		
Davies Tracy Acquah		
Victoria Prempeh		
Betty Amissah		
Solomon Amoakwa		
Francis Osei		
Grace Eileen Amissah		
Paul Takyi		
Joana Eshun		
William Ato Sekum		
Frank Akyea Bellas		
Frank Marfo		
Gifty Quaynor		
Daniel Ablorh Mensah		
Dorcas Egyir		
Isaac Bentum		
Joseph Acquah		
Seraphine Sosu		
Ebenezer Botwe		
Faustina Duah		
Francis Amangoah		
Emmanuel Mireku		
George Donkor		
Justice Ameyaw		
Barbara Baisie		
Cynthia Ofosu		
Eva Forson		
Jesse Adams		
Kate Efua Armah		
Lydia Abraham		
Kojo Essel		
Michael Cobbina		
Michael Amissah		
Frank Abotsi		
Frederick Amonoo		
Eric Otoo		
Agnes Mensah		
Georgina Owusu		
Ophelia Ocloo		
Lloyd Nelson		
Henrietta Korkor Abbey		
Sabina Sackey		
Esinam Fiakorme		
Conrad Nyamiah		
Eric Ayidah		
Edward Thomas Christian		
Doris Asamoah		
Agnes Arthur		
Charwetey		
Thelma Gidiglo		
Regina Barnes		
Elizabeth Amo-Broni		
Esther Takyi		
Samuel Hayford		
Theophilus Quayson		
Iren Amu		
Florence Phian		
Francisca Osei		
Clearance Ansah		
Opoku Nelson		
James Courage Awuah		
Abigail Agyekum		
Paul Otuteye		
Salvador Kalisti		
Evans Abeka		
Kweku Ofori		
Kwame Appiah		
Mavis Okyne		
Samuel Blagogee		
James Buah		
John William Amoah		
Elizabeth Amu Broni		
Alfred Assan		
Nicholas Amor		
Robert Amoah		
Rashid		
Eric Boafo		
Assah Benjamin		bassah@ucc.edu.gh
Francisca Boadi		babyyaaboadi@yahoo.com
Evelyn Ndur-Hansford		gashlyn27@msn.com
Evans Kobina Arhin		evanskobinaarhin@gmail.com
Francis Kwesi Kyirewiah		Kwesikyirewiah@gmail.com
Francis Isaac Dick		padekyi@yahoo.co.uk
Wilhelmina Awotwi		awotwi80@gmail.com
Doreen Preko Mensah		doreenboahen73@gmail.com
Patricia Asante		asantepatricia100@gmail.com
Saadia Alege Nee Sanda		saadia_alege@yahoo.co.uk
Rita Essel		ressel@ghanaports.gov.gh
Rockson Lambert Ayah		rockson.ayah@yahoo.com
Evelyn Orleans-Boham		essieorleans@gmail.com
Dinah Quainoo		dinahquainoo123451@gmail.com
Manasseh Portuphy		ofoep@hotmail.com
Mrs. Mercy Ansaba Budu		budumercy@yahoo.com
Nana Kweku Agyemang		iamnanakweku@gmail.com
Kojo Ewusie		kojoewusie@yahoo.com
Ivy Hyde		naadedei2001@yahoo.com
Mary Tekyi-Ansah Yaodze		arabatus@yahoo.com
Helen Lawson		abaachere1@gmail.com
Linda Anowie Fynn		anowiefynn@gmail.com
Vida Adwoa Peters		vidapeter2021@gmail.com
Mina Abu_Sakyi		msakyi97@gmail.com
Emmanuel Ampofo		ekampofo@yahoo.co.uk
Ekow Dawson-Amoah		edamoah@gmail.com
Mercy Marian Ashie		teikonye@gmail.com
Ferdinand  Ato Taylor		ffferdtaylor@gmail.com
Claribelle Wilson Ampiah (Mrs)		bclarrie@gmail.com
Leonard Mills		kyief@yahoo.com
Frank Adu Parko		frankaduparkoh38@gmail.com
Ben Kwansah		bkkwansa@ug.edu.gh
George Martin Fynn		gizoras@yahoo.com
Pamela Amos		kezzy709@yahoo.co.uk
Edwin Okoto Assnte		ibraadam04@gmail.com
Leticia Amo		ernestokyeregh@gmail.com
Geoffrey Okwan Quansah		cynthiabaah5@gmail.com
Sylvia Kotey		georgefynn.19755@yahoo.come
Adobea Clement		nanpell22@gmail.com
Ruben Speaks Ghartey		okotosounds@gmail.com
Mina Annobil		leticiaamo400@gmail.com
Kwasi Gyekye-Koranteng		geoquash2012@gmail.com
Kweku Arhin		sylviakotey892@gmail.com
Jemima Margan		adonab76@yahoo.com
Jonathan Opoku		bishopspeaks42@gmail.com
Richard Amoah		naafrimpongmaa@hotmail.com
Andrew Hammond		kwasi.gk@gmail.com
Jonas Kwamina Ampiaw		kwekuarhin2@gmail.com
Vivian Yaba Gyasi Antwi		jemimamargan@gmail.com
Addo Agyei		nabcowrfed02811@gmail.com
Kofi Abbew Nkrumah		ramoah231@gmail.com
Eunice Agyemang		1niiayi@gmail.com
Ogyadu Obuadabang Larbi		shepherdboy939@gmail.com
Solomon Inkum		viyabs@yahoo.com
Sarah Evelyn Naadu Abbew		addogift@yahoo.com
Charles Antwi		kofiabbewnkrumah@yahoo.co.uk
Kojo Yankah		uniagye1@gmail.com
Joseph E. Cobbinah		nanafobuah25@gmail.com
Samuel Ocran		ojnii@yahoo.com
Yaa Newman		adonab76@yahoo.com
Reginald Appiah-Koduah		bclarrie@gmail.com
Vida Peters		solomoninkum@gmail.com
Ruth Anane-Darko		naadueve@gmail.com
Sheila Mensah-Bonsu		antwi.kwasi@gmail.com
Akyere Frimpong Manu		kryankah@gmail.com
Kwamina		subri-1@hotmail.com
Michael Forson		sam.ocran123@gmail.com
Nike Ofori-Agyeman		yaa.newman24@yahoo.com
Carl Ankamah		bclarrie@gmail.com
Elizabeth Maame Pieterson		reggiekoo@yahoo.co.uk
Evelyn Tabiri		vidapeter2021@gmail.com
Claribelle Ampiah		ruth.darko@gmail.com
Jackline Oppong Yeboah		abonsu4@yahoo.com
Mina Owusu Sakyi		akyere.bonnahboadi@gmail.com
Sheryce		shepherdboy939@gmail.com
Sylvia Kotey		michaelforson@yahoo.co.uk
Saadia Alege		nikeoforiagyeman64@gmail.com
Vida		CAnkamah@gmail.com
Genevive Ofori-Nuako.		elizabeth.pieterson@gmail.com
Ibrahim Adams		eatwum@hotmail.com
David  Taylor		bclarrie@gmail.com
Roberta Octchere		jacklineoppong95@gmail.com
Frank Osei Kofi		msakyi97@gmail.com
Delali Blekpe		naafrimpongmaa@hotmail.com
Daniel Kwesi Sarfo		sjpoku123@yahoo.com
Cecilia Baaba Davies		sylviakotey892@gmail.com
Louis Agyenim Boateng		sylviakotey892@gmail.com
Getrude Adarkwa		saadia_alege@yahoo.co.uk
Patricia Mensah		vidapeter2021@gmail.com
Martha Estibah		 Nanafobuah25@gmail.com
Stephanie Adzimah		ibraadam04@gmail.com 
Selina Amati Doe		Davidtylor@gmail.com
Celestina Asante		asacel78@gmail.com
Barbara Chapman Grant		barbstham@yahoo.com
Yaw Simpson -Amissah		yaw_amissah @yahoo.comm
Eric Fiifi Ansah		ericfiifiansah@gmail.com
Joana Agyeman Yeboah		joanaakosuat@gmail.com
Lilian Agyei		lillianagyeilarbi@gmail.com
Dennis Fumador		delasoul@gmail.com
Millicent Nkansah		adwoa_millie@yahoo.co.uk
Claudia Owusu		claudiaowusu26@gmail.com
Kofi Addo Akorsah		kofiaddoakorsah@gmail.com
Cita Nayar		citanayar@gmail.com
Mavis  Frimpong		mafful79@gmail.com
Kwabena Oapre- Asamoah		kasamoah@arbapexbank.com
Jabina Anaman		jabinaman@gmail.com
Sheila Akumiah		sheilaahukie.akumiah@gmail.com
Phoebe  Harriet Cofie		christcofie@gmail.com
Kezia Baffoe Bonnie		kankez@gmail.com.
Gregoria Kofie		sidor123@yahoo.com
Florence Oye Okraku		flodjoe37@gmail.com
Leona Kuokor Yankholmes		leonaforall@gmail.com
Victoria Lokko		viclokko20@gmail.com
Frederick Acquah		acquah.frederick@yahoo.com
Sarah Dzamefe		sarahdzamefe6@gmail.com
Jonny Andoh Arthur		johnnyandoharthur@gmail.com
Diana Framke		framkediana@gmail.com
Edem Doamekpor		edemedoamekpor@gmail.com
Francis Amonoo		biazoconsultltd@gmail.com
David Agbeley		edagbeley@gmail.com 
Francis Asiedu		franasiedu@yahoo.com
Richard Oduro		richardoduro821@gmail.com
Adams Owusu		
Albella Quarshie		
Andrew J.Morrison		
Angela Ofori		
Assanatu Iddrisu		
Bertha Gaisie		
Bridget Yeboah		
Cecilia Yartel		
Col. Dorothy E.Tay		
Col.John Tenzii		
Dr. Korkor W.Nortey		
Dr. Regina O.Amoako-Sakyi		
Elsie Edusei		
Emmanuel Boadu		
Eric O.Adjei		
Eugene O.Brobbey		
Evelyn Abbew		
Felix Kordorwu		
Fuseina Iddrisu		
Gifty S.Abban		
Gisela M.A.Aryeetey		
Grace Apetorgbor		
Isaac  K. Amoah		
Jerome K.Worg		
Joana Rockson		
Josephine Dsane		
Kate Kwakye B		
Kwaku Antwi		
Lily Larsey		
Frazier Appeadu Malcolm		
Maxwell Bayala		
Michael Abbey		
Michael Afram Danquah		
Nadia Lamptey		
Nana Adwoa		
Randolph Osei		
Rita Arhin		
Rita Mensah B		
Shemaat Botchway		
Sophia D.Acquah		
Veronica Akanyachab		
Abdul Malik Sakande		abdulsakande@outlook.com
Abigail  Kumi		abigailkumi22@outlook.com
Abraham   Otuteye		abbynash@yahoo.com
Adelaide Aku Quarshie		adelaide_quarshie@outlook.com
Adriana  Dodoo		adriana.dodoo@outlook.com
Akwesi   Peprah		akwesi.peprah@outlook.com
Alberta  Kpeleku		
Alexander   Ansong		alexander.ansong@outlook.com
Alexander   Sasu		alexander.sasu@outlook.com
Alexander  Nimo Wiredu		alexander.wiredu@outlook.com
Alfred  Acquaye		alfred.acquaye@outlook.com
Andrew  Svanikier		andrew.svaikier@outlook.com
Angela   Serwaa Hayford		angela.hayford@outlook.com
Anukware Ekua Ofori		anukware.ofori@outlook.com
Ato  Williams		
Barbara  Baah		barbarabaah11@outlook.com
Belinda   Debrah		belindadebrah@expresso.telecom.com
Benjamin  Otchere		benota27@gmail.com
Benjamin  Otoo		johnbrown05@outlook.com
Bernard   Abbequaye		paapa244@yahoo.com
Bernard   Attakora-Yeboah		
Bridget  Helegbe		
Bright   Paintsil		bright.paintsil@yahoo.com
Bryndenise   Allotey		adbryn@yahoo.com
Charles  Bentil-Arthur		charles.bentil-arthur@outlook.com
Charles Oteng Acquaye		
Charlotte Danso Parry-Hanson		charlotte.parry-hanson@outlook.com
Christiana  Kilson		christiana.kilson@outlook.com
Cindy  Afi Letcher		cindal99@yahoo.co.uk
Clarissa  Oman		clarissa.oman@outlook.com
Comfort   Hall		conniehall@yahoo.com
Cynthia   Birago		cynthia.birago@outlook.com
Cynthia   Mensah		cynthiamensah44@outlook.com
Daisy   Baidoo		daisy.baidoo@outlook.com
Daniel   Akpabey		akpabey05@yahoo.com
Daniel Kuntu Blankson		danielblankson50@outlook.com
Danita   Addo		danitaaddo@outlook.com
David  Ampah-Korsah		dakes79@gmail.com
David  Asare		kobigem2008@gmail.com
Debbie   Dodoo		debbiedodoo@yahoo.com
Delali  Kofi  Adayi		kaddella@yahoo.com
Dorcas    Amo-Broni		dorciejo@yahoo.com
Dorothy   Arthur		dafxxx99@yahoo.com
Ebenezer   Otoo		dorothy.arthur@outlook.com
Ebenezer  Smith  Frempong		niieben@yahoo.com
Edith   Owusu		Free7brainy@yahoo.com
Edith  Mabel Lamptey		edinashus@yahoo.com
Edna  Duodo Baafi		Edmalaent@gmail.com
Edwin  Gyamfi		ednacic@yahoo.com
Elfreda  Kinful		rainbowcc20@gmail.com
Elizabeth   Kumi		
Elizabeth   Nsaidoo		liza244@yahoo.co.uk
Emmanuel  0doi Kpobi		einsaidoo2002@yahoo.co.uk
Emmanuel  Nana  Mussey		keliada8008@yahoo.com
Emmanuel Ola   Williams		wilmanuel 86@yahoo.com
Eric  Appiah		
Eric  Ohene  Wiafe		eohenewiafe@yahoo.com
Erica   Nkansah		marmianne@yahoo.co.uk
Ernest  Essien		
Ernest  Obeng		anjalianwar6@gmail.com
Ernestina  Osei		
Erskine  Amoakuh		
Esi   Pitt		esipitt@yahoo.com
Estella  Baiden		
Esther  Naa Dedei Amoah		Esthynaadedei@gmail.com
Eunice   Shamo		shamoeunice@hotmail.com
Eunice  Gyimah		
Evans   Amponsah		
Faustina  Abbey		faustinaabbey@outlook.com
Francis   Boakye		poshgh2@yahoo.co.uk
Francis  Edem Noviewoo		francis.noviewoo@outlook.com
Francisca  Dadzie		francisca.dadzie@outlook.com
Frank  Debrah		frank.debrah@outlook.com
Frank  Idan		frank.idan@outlook.com
Frank Kwabena Mensah		frank.mensah@outlook.com
George  Nana Akwasi Agyemang Prempeh		george.agyemang prempeh@outlook.com
Getrude   Owusu-Asante		trudy09@yahoo.com
Gilbert Kwesi Mensah		gilkwesi@yahoo.com
Grace  Sackey		grace.sackey@outlook.com
Hagar   Bandoh		hagar.bandoh@outlook.com
Hajara  Rashid		hajara.rashid@outlook.com
Hannah  Asiedu		hannah.asiedu@outlook.com
Harriet   Ankoma		hertsom@yahoo.com
Harriet   Edjameh		harriet.edjameh@outlook.com
Harriet   Ofosu		hetty244@yahoo.com
Harriet   Osabre		harriet.osabre@outlook.com
Harriet  Klorkor Botchway		hkbotchway@gmail.com
Henry Nii Nai Sowah		henry.sowah@outlook.com
Irene  Antwi		ireneantwi94@gmail.com
Isaac   Annan		isaacannan21@protonmail.com
Isaac   Mensah		isaac.mensah@outlook.com
Isabella  Dose		isabella.dose@outlook.com
Ismail  Huwaladu		ismail.huwaladu@outlook.com
Ivan   Sena Kwasi Samey		delesseps2000@yahoo.com
Ivana   Tei-Wayo		maaivana@yahoo.com
Josephine   Boateng		josephine.boateng@outlook.com
Josephine   Bonney		josephine.bonney@outlook.com
Josephine   Komla		jossiekom@gmail.com
Josephine   Manu		josephinemanu@rocketmail.com
Juanita  Nyemitei		juanita.nyemitei@outlook.com
Juliana   Ejimandus		juliana.ejimandus@outlook.com
Kobina    Fordjour Codjoe		kobina.codjoe@outlook.com
Kofi   Marfo		kofimarfo@yahoo.com
Kojo   Roberts		kojoroberts@gmail.com
Kojo  Osei Quayson-Sackey		kojo.quayson-sackey@outlook.com
Kwabena  Asiedu		kwabena.asiedu@outlook.com
Kwame   Boateng		boateng-kwame@gmail.com
Kwame  Gyamfi Boateng		kwame.boateng@outlook.com
Linda  Sackie - Mensah		linda.mensah@outlook.com
Marian  Korley		marian.korley@outlook.com
Marjorie  Boateng		marjorie.boateng@outlook.com
Mary   Appiah-Danquah		mary.appiah-danquah@outlook.com
Mary   Warden		mary.warden@outlook.com
Mary  Akushika Elorm Avevor		avemaria 233@yahoo.com
Matilda  Appiah		matilda.appiah@outlook.com
Maud  Ofori-Affoh		maud.ofori-affoh@outlook.com
Mavis   Love Gyimah Boateng		gyimahboatengmavis@yahoo.co.uk
Maxlein  Addai-Minkah		maxlein.addai-minkah@outlook.com
Murtala  Idrissu		murtala.idrissu@outlook.com
Nana  Adwoa  Kissiedu		adwoa244@yahoo.com
Nana  Attaku Mussey		nana.mussey@outlook.com
Nana  Yeboah  Abankwah		jhorghin@yahoo.com
Nancy Akweley Lamptey		nancylamptey@outlook.com
Naomi  Amakie  Odonkor		chichisnaa@yahoo.com
Nathaniel Oko Lamptey		nathaniel.lamptey@outlook.com
Nii   Commey		niirocky@yahoo.com
Noble  Garbrah		loveligallus@yahoo.com
Ocansey		ocansey.@outlook.com
Ophelia   Azumah		wishes_kiki@yahoo.com
Oppong   Gyansah		oppong.gyansah@outlook.com
Osumanu-Suleman Luki Amidu		osumanu-suleman.amidu@outlook.com
Patience  Nanor  Quadzi		pattynocles@yahoo.com
Patrick   Osei-Affum		patosaf@yahoo.com
Patrick  Kofi  Quansah		patrick.quansah@outlook.com
Prince   Asiedu		prince.asiedu@outlook.com
Prince   Obour		heirsgh@yahoo.com
Priscilla   Mensah		priscilla.mensah@outlook.com
Racheal  Afutu Lawson		a-afutu@yahoo.com
Rahman  Kojo Baidoo		randaz@yahoo.com
Rashida  Hamidu-Chodi		rashida.hamidu-chodi@outlook.com
Rebecca   Asare		becklove@yahoo.com
Reginald  Anang		reginald.anang@outlook.com
Rhoda  Armah		rhoda.armah@outlook.com
Richard   Ansong		richard.ansong@outlook.com
Rita  Agyeman  Duah		maamefrema@yahoo.com
Robert   Sackey		robert.sackey@gmail.com
Rosemond   Mbillah		funnyrd@hotmail.com
Rosemond  Adonteng Amoah		rosemond.amoah@outlook.com
Ruth Afrema  Sackey		ruth_sackey@outlook.com
Sadia   Abu		sweetsadyy@yahoo.com
Samuel   Assumang		samask2001@yahoo.co.uk
Samuel  Allan Abban		samuel.abban@yahoo.com
Samuel  Nana  Debrah		samuel.debrah@outlook.com
Samuel  Nii  Okai		samuel.okai@outlook.com
Sherry   Obeng		sherryobeng@yahoo.com
Stanley   Dorglo		dorgmarius@gmail.com
Stephen  Dwamena		stephen.dwamena@outlook.com
Suzette   Jackson		suzette.jackson@outlook.com
Sylvia   Davis		syltengg@gmail.com
Theresa   Twumasi		thesstwum@yahoo.com
Theresa  Darko		theresadarko@outlook.com
Theresa  Twum		tgyekyetwum@yahoomail.com
Kofi Aseidu		turbo.@outlook.com
Vera  Paulette Efua Nyame		aboagyevera5@gmail.com
Veronica Kenyah Armooh		veronica_armooh@outlook.com
Victoria  Attakora		victoria.attakora@outlook.com
Victoria  Attakorah		victoria.attakorah@outlook.com
Vida  Antwi		vidash2k1@yahoo.com
Vitus  Adams		vitus.adams@outlook.com
Wilhelmina   Arthur		arthurwilhemina4@gmail.com
William   Agyemang		agyemang.william@gmail.com
William  Bondzie Sam Parker		parkerblaze@yahoo.com
William  Robertson		williamrobertson70@outlook.com
William Kwaku  Tawiah		wtawiah@yahoo.com"""

def parse_voters(data):
    voters = []
    voter_id = 1001
    
    lines = [line.strip() for line in data.strip().split('\n') if line.strip()]
    
    for line in lines:
        # Split by tab
        parts = [p.strip() for p in line.split('\t')]
        
        # Extract name, phone, email
        name = parts[0] if len(parts) > 0 else ""
        phone = parts[1] if len(parts) > 1 else ""
        email = parts[2] if len(parts) > 2 else ""
        
        # Clean up special characters
        name = name.replace('⁠', '').strip()
        phone = phone.replace('⁠', '').strip()
        email = email.strip()
        
        if name:  # Only add if we have a name
            voters.append({
                "id": voter_id,
                "name": name,
                "pin": f"pin{voter_id}",
                "phone": phone,
                "email": email
            })
            voter_id += 1
    
    return voters

# Generate voters
voters = parse_voters(voter_data)

# Write to JSON file
output_file = "voters.json"
with open(output_file, 'w', encoding='utf-8') as f:
    json.dump(voters, f, indent=2, ensure_ascii=False)

print(f"✓ Generated {len(voters)} voters in {output_file}")
print(f"✓ ID range: {voters[0]['id']} - {voters[-1]['id']}")
print(f"✓ Voters with emails: {sum(1 for v in voters if v['email'])}")
print(f"✓ Voters with phones: {sum(1 for v in voters if v['phone'])}")
