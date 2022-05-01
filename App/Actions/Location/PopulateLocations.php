<?php

namespace App\Actions\Location;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Traits\HasResourceStatus;
use Illuminate\Support\Facades\DB;

class PopulateLocations extends Action
{
    use HasResourceStatus;

    protected $request;
    protected $list = '{
      "data": [
          {
              "id": 1,
              "country": "Nigeria",
              "code": "NG",
              "currency": "NGN",
              "symbol": "₦",
              "states": [
                  {
                      "id": 1,
                      "state": "Abia State",
                      "cities": [
                          {
                              "id": 1,
                              "city": "Aba"
                          },
                          {
                              "id": 2,
                              "city": "Amaigbo"
                          },
                          {
                              "id": 3,
                              "city": "Arochukwu"
                          },
                          {
                              "id": 4,
                              "city": "Bende"
                          },
                          {
                              "id": 5,
                              "city": "Ohafia-Ifigh"
                          },
                          {
                              "id": 6,
                              "city": "Umuahia"
                          }
                      ]
                  },
                  {
                      "id": 2,
                      "state": "Adamawa State",
                      "cities": [
                          {
                              "id": 7,
                              "city": "Ganye"
                          },
                          {
                              "id": 8,
                              "city": "Gombi"
                          },
                          {
                              "id": 9,
                              "city": "Holma"
                          },
                          {
                              "id": 10,
                              "city": "Jimeta"
                          },
                          {
                              "id": 11,
                              "city": "Madagali"
                          },
                          {
                              "id": 12,
                              "city": "Mayo-Belwa"
                          },
                          {
                              "id": 13,
                              "city": "Mubi"
                          },
                          {
                              "id": 14,
                              "city": "Ngurore"
                          },
                          {
                              "id": 15,
                              "city": "Numan"
                          },
                          {
                              "id": 16,
                              "city": "Toungo"
                          },
                          {
                              "id": 17,
                              "city": "Yola"
                          }
                      ]
                  },
                  {
                      "id": 3,
                      "state": "Akwa Ibom State",
                      "cities": [
                          {
                              "id": 18,
                              "city": "Eket"
                          },
                          {
                              "id": 19,
                              "city": "Esuk Oron"
                          },
                          {
                              "id": 20,
                              "city": "Ikot Ekpene"
                          },
                          {
                              "id": 21,
                              "city": "Itu"
                          },
                          {
                              "id": 22,
                              "city": "Uyo"
                          }
                      ]
                  },
                  {
                      "id": 4,
                      "state": "Anambra State",
                      "cities": [
                          {
                              "id": 23,
                              "city": "Agulu"
                          },
                          {
                              "id": 24,
                              "city": "Atani"
                          },
                          {
                              "id": 25,
                              "city": "Awka"
                          },
                          {
                              "id": 26,
                              "city": "Enugu-Ukwu"
                          },
                          {
                              "id": 27,
                              "city": "Igbo-Ukwu"
                          },
                          {
                              "id": 28,
                              "city": "Ihiala"
                          },
                          {
                              "id": 29,
                              "city": "Nkpor"
                          },
                          {
                              "id": 30,
                              "city": "Nnewi"
                          },
                          {
                              "id": 31,
                              "city": "Onitsha"
                          },
                          {
                              "id": 32,
                              "city": "Ozubulu"
                          },
                          {
                              "id": 33,
                              "city": "Uga"
                          },
                          {
                              "id": 34,
                              "city": "Uruobo-Okija"
                          }
                      ]
                  },
                  {
                      "id": 5,
                      "state": "Bauchi State",
                      "cities": [
                          {
                              "id": 35,
                              "city": "Azare"
                          },
                          {
                              "id": 36,
                              "city": "Bauchi"
                          },
                          {
                              "id": 37,
                              "city": "Boi"
                          },
                          {
                              "id": 38,
                              "city": "Bununu"
                          },
                          {
                              "id": 39,
                              "city": "Darazo"
                          },
                          {
                              "id": 40,
                              "city": "Dass"
                          },
                          {
                              "id": 41,
                              "city": "Dindima"
                          },
                          {
                              "id": 42,
                              "city": "Disina"
                          },
                          {
                              "id": 43,
                              "city": "Gabarin"
                          },
                          {
                              "id": 44,
                              "city": "Gwaram"
                          },
                          {
                              "id": 45,
                              "city": "Kari"
                          },
                          {
                              "id": 46,
                              "city": "Lame"
                          },
                          {
                              "id": 47,
                              "city": "Lere"
                          },
                          {
                              "id": 48,
                              "city": "Madara"
                          },
                          {
                              "id": 49,
                              "city": "Misau"
                          },
                          {
                              "id": 50,
                              "city": "Sade"
                          },
                          {
                              "id": 51,
                              "city": "Yamrat"
                          },
                          {
                              "id": 52,
                              "city": "Yanda Bayo"
                          },
                          {
                              "id": 53,
                              "city": "Yuli"
                          },
                          {
                              "id": 54,
                              "city": "Zadawa"
                          },
                          {
                              "id": 55,
                              "city": "Zalanga"
                          }
                      ]
                  },
                  {
                      "id": 6,
                      "state": "Bayelsa State",
                      "cities": [
                          {
                              "id": 56,
                              "city": "Amassoma"
                          },
                          {
                              "id": 57,
                              "city": "Twon-Brass"
                          },
                          {
                              "id": 58,
                              "city": "Yenagoa"
                          }
                      ]
                  },
                  {
                      "id": 7,
                      "state": "Benue State",
                      "cities": [
                          {
                              "id": 59,
                              "city": "Aliade"
                          },
                          {
                              "id": 60,
                              "city": "Boju"
                          },
                          {
                              "id": 61,
                              "city": "Igbor"
                          },
                          {
                              "id": 62,
                              "city": "Makurdi"
                          },
                          {
                              "id": 63,
                              "city": "Ochobo"
                          },
                          {
                              "id": 64,
                              "city": "Otukpa"
                          },
                          {
                              "id": 65,
                              "city": "Takum"
                          },
                          {
                              "id": 66,
                              "city": "Ugbokpo"
                          },
                          {
                              "id": 67,
                              "city": "Yandev"
                          },
                          {
                              "id": 68,
                              "city": "Zaki Biam"
                          }
                      ]
                  },
                  {
                      "id": 8,
                      "state": "Borno State",
                      "cities": [
                          {
                              "id": 69,
                              "city": "Bama"
                          },
                          {
                              "id": 70,
                              "city": "Benisheikh"
                          },
                          {
                              "id": 71,
                              "city": "Biu"
                          },
                          {
                              "id": 72,
                              "city": "Bornu Yassu"
                          },
                          {
                              "id": 73,
                              "city": "Damasak"
                          },
                          {
                              "id": 74,
                              "city": "Damboa"
                          },
                          {
                              "id": 75,
                              "city": "Dikwa"
                          },
                          {
                              "id": 76,
                              "city": "Gamboru"
                          },
                          {
                              "id": 77,
                              "city": "Gwoza"
                          },
                          {
                              "id": 78,
                              "city": "Kukawa"
                          },
                          {
                              "id": 79,
                              "city": "Magumeri"
                          },
                          {
                              "id": 80,
                              "city": "Maiduguri"
                          },
                          {
                              "id": 81,
                              "city": "Marte"
                          },
                          {
                              "id": 82,
                              "city": "Miringa"
                          },
                          {
                              "id": 83,
                              "city": "Monguno"
                          },
                          {
                              "id": 84,
                              "city": "Ngala"
                          },
                          {
                              "id": 85,
                              "city": "Shaffa"
                          },
                          {
                              "id": 86,
                              "city": "Shani"
                          },
                          {
                              "id": 87,
                              "city": "Tokombere"
                          },
                          {
                              "id": 88,
                              "city": "Uba"
                          },
                          {
                              "id": 89,
                              "city": "Wuyo"
                          },
                          {
                              "id": 90,
                              "city": "Yajiwa"
                          }
                      ]
                  },
                  {
                      "id": 9,
                      "state": "Cross River State",
                      "cities": [
                          {
                              "id": 91,
                              "city": "Akankpa"
                          },
                          {
                              "id": 92,
                              "city": "Calabar"
                          },
                          {
                              "id": 93,
                              "city": "Gakem"
                          },
                          {
                              "id": 94,
                              "city": "Ikang"
                          },
                          {
                              "id": 95,
                              "city": "Ugep"
                          }
                      ]
                  },
                  {
                      "id": 10,
                      "state": "Delta State",
                      "cities": [
                          {
                              "id": 96,
                              "city": "Abraka"
                          },
                          {
                              "id": 97,
                              "city": "Agbor"
                          },
                          {
                              "id": 98,
                              "city": "Asaba"
                          },
                          {
                              "id": 99,
                              "city": "Bomadi"
                          },
                          {
                              "id": 100,
                              "city": "Burutu"
                          },
                          {
                              "id": 101,
                              "city": "Kwale"
                          },
                          {
                              "id": 102,
                              "city": "Obiaruku"
                          },
                          {
                              "id": 103,
                              "city": "Ogwashi-Uku"
                          },
                          {
                              "id": 104,
                              "city": "Orerokpe"
                          },
                          {
                              "id": 105,
                              "city": "Patani"
                          },
                          {
                              "id": 106,
                              "city": "Sapele"
                          },
                          {
                              "id": 107,
                              "city": "Ughelli"
                          },
                          {
                              "id": 108,
                              "city": "Umunede"
                          },
                          {
                              "id": 109,
                              "city": "Warri"
                          }
                      ]
                  },
                  {
                      "id": 11,
                      "state": "Ebonyi State",
                      "cities": [
                          {
                              "id": 110,
                              "city": "Abakaliki"
                          },
                          {
                              "id": 111,
                              "city": "Afikpo"
                          },
                          {
                              "id": 112,
                              "city": "Effium"
                          },
                          {
                              "id": 113,
                              "city": "Ezza-Ohu"
                          },
                          {
                              "id": 114,
                              "city": "Isieke"
                          }
                      ]
                  },
                  {
                      "id": 12,
                      "state": "Edo State",
                      "cities": [
                          {
                              "id": 115,
                              "city": "Agenebode"
                          },
                          {
                              "id": 116,
                              "city": "Auchi"
                          },
                          {
                              "id": 117,
                              "city": "Benin City"
                          },
                          {
                              "id": 118,
                              "city": "Ekpoma"
                          },
                          {
                              "id": 119,
                              "city": "Igarra"
                          },
                          {
                              "id": 120,
                              "city": "Illushi"
                          },
                          {
                              "id": 121,
                              "city": "Siluko"
                          },
                          {
                              "id": 122,
                              "city": "Ubiaja"
                          },
                          {
                              "id": 123,
                              "city": "Uromi"
                          }
                      ]
                  },
                  {
                      "id": 13,
                      "state": "Ekiti State",
                      "cities": [
                          {
                              "id": 124,
                              "city": "Ado-Ekiti"
                          },
                          {
                              "id": 125,
                              "city": "Aramoko-Ekiti"
                          },
                          {
                              "id": 126,
                              "city": "Efon-Alaaye"
                          },
                          {
                              "id": 127,
                              "city": "Emure-Ekiti"
                          },
                          {
                              "id": 128,
                              "city": "Ifaki"
                          },
                          {
                              "id": 129,
                              "city": "Igbara-Odo"
                          },
                          {
                              "id": 130,
                              "city": "Igede-Ekiti"
                          },
                          {
                              "id": 131,
                              "city": "Ijero-Ekiti"
                          },
                          {
                              "id": 132,
                              "city": "Ikere-Ekiti"
                          },
                          {
                              "id": 133,
                              "city": "Ipoti"
                          },
                          {
                              "id": 134,
                              "city": "Ise-Ekiti"
                          },
                          {
                              "id": 135,
                              "city": "Oke Ila"
                          },
                          {
                              "id": 136,
                              "city": "Omuo-Ekiti"
                          }
                      ]
                  },
                  {
                      "id": 14,
                      "state": "Enugu State",
                      "cities": [
                          {
                              "id": 137,
                              "city": "Adani"
                          },
                          {
                              "id": 138,
                              "city": "Ake-Eze"
                          },
                          {
                              "id": 139,
                              "city": "Aku"
                          },
                          {
                              "id": 140,
                              "city": "Amagunze"
                          },
                          {
                              "id": 141,
                              "city": "Awgu"
                          },
                          {
                              "id": 142,
                              "city": "Eha Amufu"
                          },
                          {
                              "id": 143,
                              "city": "Enugu"
                          },
                          {
                              "id": 144,
                              "city": "Enugu-Ezike"
                          },
                          {
                              "id": 145,
                              "city": "Ete"
                          },
                          {
                              "id": 146,
                              "city": "Ikem"
                          },
                          {
                              "id": 147,
                              "city": "Mberubu"
                          },
                          {
                              "id": 148,
                              "city": "Nsukka"
                          },
                          {
                              "id": 149,
                              "city": "Obolo-Eke (1)"
                          },
                          {
                              "id": 150,
                              "city": "Opi"
                          },
                          {
                              "id": 151,
                              "city": "Udi"
                          }
                      ]
                  },
                  {
                      "id": 15,
                      "state": "Federal Capital Territory",
                      "cities": [
                          {
                              "id": 152,
                              "city": "Abuja"
                          },
                          {
                              "id": 153,
                              "city": "Kuje"
                          },
                          {
                              "id": 154,
                              "city": "Kwali"
                          },
                          {
                              "id": 155,
                              "city": "Madala"
                          }
                      ]
                  },
                  {
                      "id": 16,
                      "state": "Gombe State",
                      "cities": [
                          {
                              "id": 156,
                              "city": "Akko"
                          },
                          {
                              "id": 157,
                              "city": "Bara"
                          },
                          {
                              "id": 158,
                              "city": "Billiri"
                          },
                          {
                              "id": 159,
                              "city": "Dadiya"
                          },
                          {
                              "id": 160,
                              "city": "Deba"
                          },
                          {
                              "id": 161,
                              "city": "Dukku"
                          },
                          {
                              "id": 162,
                              "city": "Garko"
                          },
                          {
                              "id": 163,
                              "city": "Gombe"
                          },
                          {
                              "id": 164,
                              "city": "Hinna"
                          },
                          {
                              "id": 165,
                              "city": "Kafarati"
                          },
                          {
                              "id": 166,
                              "city": "Kaltungo"
                          },
                          {
                              "id": 167,
                              "city": "Kumo"
                          },
                          {
                              "id": 168,
                              "city": "Nafada"
                          },
                          {
                              "id": 169,
                              "city": "Pindiga"
                          }
                      ]
                  },
                  {
                      "id": 17,
                      "state": "Imo State",
                      "cities": [
                          {
                              "id": 170,
                              "city": "Iho"
                          },
                          {
                              "id": 171,
                              "city": "Oguta"
                          },
                          {
                              "id": 172,
                              "city": "Okigwe"
                          },
                          {
                              "id": 173,
                              "city": "Orlu"
                          },
                          {
                              "id": 174,
                              "city": "Orodo"
                          },
                          {
                              "id": 175,
                              "city": "Owerri"
                          }
                      ]
                  },
                  {
                      "id": 18,
                      "state": "Jigawa State",
                      "cities": [
                          {
                              "id": 176,
                              "city": "Babura"
                          },
                          {
                              "id": 177,
                              "city": "Birnin Kudu"
                          },
                          {
                              "id": 178,
                              "city": "Birniwa"
                          },
                          {
                              "id": 179,
                              "city": "Dutse"
                          },
                          {
                              "id": 180,
                              "city": "Gagarawa"
                          },
                          {
                              "id": 181,
                              "city": "Gumel"
                          },
                          {
                              "id": 182,
                              "city": "Gwaram"
                          },
                          {
                              "id": 183,
                              "city": "Hadejia"
                          },
                          {
                              "id": 184,
                              "city": "Kafin Hausa"
                          },
                          {
                              "id": 185,
                              "city": "Kazaure"
                          },
                          {
                              "id": 186,
                              "city": "Kiyawa"
                          },
                          {
                              "id": 187,
                              "city": "Mallammaduri"
                          },
                          {
                              "id": 188,
                              "city": "Ringim"
                          },
                          {
                              "id": 189,
                              "city": "Samamiya"
                          }
                      ]
                  },
                  {
                      "id": 19,
                      "state": "Kaduna State",
                      "cities": [
                          {
                              "id": 190,
                              "city": "Anchau"
                          },
                          {
                              "id": 191,
                              "city": "Burumburum"
                          },
                          {
                              "id": 192,
                              "city": "Dutsen Wai"
                          },
                          {
                              "id": 193,
                              "city": "Hunkuyi"
                          },
                          {
                              "id": 194,
                              "city": "Kachia"
                          },
                          {
                              "id": 195,
                              "city": "Kaduna"
                          },
                          {
                              "id": 196,
                              "city": "Kafanchan"
                          },
                          {
                              "id": 197,
                              "city": "Kagoro"
                          },
                          {
                              "id": 198,
                              "city": "Kajuru"
                          },
                          {
                              "id": 199,
                              "city": "Kujama"
                          },
                          {
                              "id": 200,
                              "city": "Lere"
                          },
                          {
                              "id": 201,
                              "city": "Mando"
                          },
                          {
                              "id": 202,
                              "city": "Saminaka"
                          },
                          {
                              "id": 203,
                              "city": "Soba"
                          },
                          {
                              "id": 204,
                              "city": "Sofo-Birnin-Gwari"
                          },
                          {
                              "id": 205,
                              "city": "Zaria"
                          }
                      ]
                  },
                  {
                      "id": 20,
                      "state": "Kano State",
                      "cities": [
                          {
                              "id": 206,
                              "city": "Dan Gora"
                          },
                          {
                              "id": 207,
                              "city": "Gaya"
                          },
                          {
                              "id": 208,
                              "city": "Kano"
                          }
                      ]
                  },
                  {
                      "id": 21,
                      "state": "Katsina State",
                      "cities": [
                          {
                              "id": 209,
                              "city": "Danja"
                          },
                          {
                              "id": 210,
                              "city": "Dankama"
                          },
                          {
                              "id": 211,
                              "city": "Daura"
                          },
                          {
                              "id": 212,
                              "city": "Dutsin-Ma"
                          },
                          {
                              "id": 213,
                              "city": "Funtua"
                          },
                          {
                              "id": 214,
                              "city": "Gora"
                          },
                          {
                              "id": 215,
                              "city": "Jibia"
                          },
                          {
                              "id": 216,
                              "city": "Jikamshi"
                          },
                          {
                              "id": 217,
                              "city": "Kankara"
                          },
                          {
                              "id": 218,
                              "city": "Katsina"
                          },
                          {
                              "id": 219,
                              "city": "Mashi"
                          },
                          {
                              "id": 220,
                              "city": "Ruma"
                          },
                          {
                              "id": 221,
                              "city": "Runka"
                          },
                          {
                              "id": 222,
                              "city": "Wagini"
                          }
                      ]
                  },
                  {
                      "id": 22,
                      "state": "Kebbi State",
                      "cities": [
                          {
                              "id": 223,
                              "city": "Argungu"
                          },
                          {
                              "id": 224,
                              "city": "Bagudo"
                          },
                          {
                              "id": 225,
                              "city": "Bena"
                          },
                          {
                              "id": 226,
                              "city": "Bin Yauri"
                          },
                          {
                              "id": 227,
                              "city": "Birnin Kebbi"
                          },
                          {
                              "id": 228,
                              "city": "Dabai"
                          },
                          {
                              "id": 229,
                              "city": "Dakingari"
                          },
                          {
                              "id": 230,
                              "city": "Gulma"
                          },
                          {
                              "id": 231,
                              "city": "Gwandu"
                          },
                          {
                              "id": 232,
                              "city": "Jega"
                          },
                          {
                              "id": 233,
                              "city": "Kamba"
                          },
                          {
                              "id": 234,
                              "city": "Kangiwa"
                          },
                          {
                              "id": 235,
                              "city": "Kende"
                          },
                          {
                              "id": 236,
                              "city": "Mahuta"
                          },
                          {
                              "id": 237,
                              "city": "Maiyama"
                          },
                          {
                              "id": 238,
                              "city": "Shanga"
                          },
                          {
                              "id": 239,
                              "city": "Wasagu"
                          },
                          {
                              "id": 240,
                              "city": "Zuru"
                          }
                      ]
                  },
                  {
                      "id": 23,
                      "state": "Kogi State",
                      "cities": [
                          {
                              "id": 241,
                              "city": "Abocho"
                          },
                          {
                              "id": 242,
                              "city": "Adoru"
                          },
                          {
                              "id": 243,
                              "city": "Ankpa"
                          },
                          {
                              "id": 244,
                              "city": "Bugana"
                          },
                          {
                              "id": 245,
                              "city": "Dekina"
                          },
                          {
                              "id": 246,
                              "city": "Egbe"
                          },
                          {
                              "id": 247,
                              "city": "Icheu"
                          },
                          {
                              "id": 248,
                              "city": "Idah"
                          },
                          {
                              "id": 249,
                              "city": "Isanlu-Itedoijowa"
                          },
                          {
                              "id": 250,
                              "city": "Kabba"
                          },
                          {
                              "id": 251,
                              "city": "Koton-Karfe"
                          },
                          {
                              "id": 252,
                              "city": "Lokoja"
                          },
                          {
                              "id": 253,
                              "city": "Ogaminana"
                          },
                          {
                              "id": 254,
                              "city": "Ogurugu"
                          },
                          {
                              "id": 255,
                              "city": "Okene"
                          }
                      ]
                  },
                  {
                      "id": 24,
                      "state": "Kwara State",
                      "cities": [
                          {
                              "id": 256,
                              "city": "Ajasse Ipo"
                          },
                          {
                              "id": 257,
                              "city": "Bode Saadu"
                          },
                          {
                              "id": 258,
                              "city": "Gwasero"
                          },
                          {
                              "id": 259,
                              "city": "Ilorin"
                          },
                          {
                              "id": 260,
                              "city": "Jebba"
                          },
                          {
                              "id": 261,
                              "city": "Kaiama"
                          },
                          {
                              "id": 262,
                              "city": "Lafiagi"
                          },
                          {
                              "id": 263,
                              "city": "Offa"
                          },
                          {
                              "id": 264,
                              "city": "Okuta"
                          },
                          {
                              "id": 265,
                              "city": "Omu-Aran"
                          },
                          {
                              "id": 266,
                              "city": "Patigi"
                          },
                          {
                              "id": 267,
                              "city": "Suya"
                          },
                          {
                              "id": 268,
                              "city": "Yashikera"
                          }
                      ]
                  },
                  {
                      "id": 25,
                      "state": "Lagos",
                      "cities": [
                          {
                              "id": 269,
                              "city": "Apapa"
                          },
                          {
                              "id": 270,
                              "city": "Badagry"
                          },
                          {
                              "id": 271,
                              "city": "Ebute Ikorodu"
                          },
                          {
                              "id": 272,
                              "city": "Ejirin"
                          },
                          {
                              "id": 273,
                              "city": "Epe"
                          },
                          {
                              "id": 274,
                              "city": "Ikeja"
                          },
                          {
                              "id": 275,
                              "city": "Lagos"
                          },
                          {
                              "id": 276,
                              "city": "Makoko"
                          }
                      ]
                  },
                  {
                      "id": 26,
                      "state": "Nasarawa State",
                      "cities": [
                          {
                              "id": 277,
                              "city": "Buga"
                          },
                          {
                              "id": 278,
                              "city": "Doma"
                          },
                          {
                              "id": 279,
                              "city": "Keffi"
                          },
                          {
                              "id": 280,
                              "city": "Lafia"
                          },
                          {
                              "id": 281,
                              "city": "Nasarawa"
                          },
                          {
                              "id": 282,
                              "city": "Wamba"
                          }
                      ]
                  },
                  {
                      "id": 27,
                      "state": "Niger State",
                      "cities": [
                          {
                              "id": 283,
                              "city": "Auna"
                          },
                          {
                              "id": 284,
                              "city": "Babana"
                          },
                          {
                              "id": 285,
                              "city": "Badeggi"
                          },
                          {
                              "id": 286,
                              "city": "Baro"
                          },
                          {
                              "id": 287,
                              "city": "Bokani"
                          },
                          {
                              "id": 288,
                              "city": "Duku"
                          },
                          {
                              "id": 289,
                              "city": "Ibeto"
                          },
                          {
                              "id": 290,
                              "city": "Konkwesso"
                          },
                          {
                              "id": 291,
                              "city": "Kontagora"
                          },
                          {
                              "id": 292,
                              "city": "Kusheriki"
                          },
                          {
                              "id": 293,
                              "city": "Kuta"
                          },
                          {
                              "id": 294,
                              "city": "Lapai"
                          },
                          {
                              "id": 295,
                              "city": "Minna"
                          },
                          {
                              "id": 296,
                              "city": "New Shagunnu"
                          },
                          {
                              "id": 297,
                              "city": "Suleja"
                          },
                          {
                              "id": 298,
                              "city": "Tegina"
                          },
                          {
                              "id": 299,
                              "city": "Ukata"
                          },
                          {
                              "id": 300,
                              "city": "Wawa"
                          },
                          {
                              "id": 301,
                              "city": "Zungeru"
                          }
                      ]
                  },
                  {
                      "id": 28,
                      "state": "Ogun State",
                      "cities": [
                          {
                              "id": 302,
                              "city": "Abeokuta"
                          },
                          {
                              "id": 303,
                              "city": "Ado Odo"
                          },
                          {
                              "id": 304,
                              "city": "Idi Iroko"
                          },
                          {
                              "id": 305,
                              "city": "Ifo"
                          },
                          {
                              "id": 306,
                              "city": "Ijebu-Ife"
                          },
                          {
                              "id": 307,
                              "city": "Ijebu-Igbo"
                          },
                          {
                              "id": 308,
                              "city": "Ijebu-Ode"
                          },
                          {
                              "id": 309,
                              "city": "Ilaro"
                          },
                          {
                              "id": 310,
                              "city": "Imeko"
                          },
                          {
                              "id": 311,
                              "city": "Iperu"
                          },
                          {
                              "id": 312,
                              "city": "Isara"
                          },
                          {
                              "id": 313,
                              "city": "Owode"
                          }
                      ]
                  },
                  {
                      "id": 29,
                      "state": "Ondo State",
                      "cities": [
                          {
                              "id": 314,
                              "city": "Agbabu"
                          },
                          {
                              "id": 315,
                              "city": "Akure"
                          },
                          {
                              "id": 316,
                              "city": "Idanre"
                          },
                          {
                              "id": 317,
                              "city": "Ifon"
                          },
                          {
                              "id": 318,
                              "city": "Ilare"
                          },
                          {
                              "id": 319,
                              "city": "Ode"
                          },
                          {
                              "id": 320,
                              "city": "Ondo"
                          },
                          {
                              "id": 321,
                              "city": "Ore"
                          },
                          {
                              "id": 322,
                              "city": "Owo"
                          }
                      ]
                  },
                  {
                      "id": 30,
                      "state": "Osun State",
                      "cities": [
                          {
                              "id": 323,
                              "city": "Apomu"
                          },
                          {
                              "id": 324,
                              "city": "Ejigbo"
                          },
                          {
                              "id": 325,
                              "city": "Gbongan"
                          },
                          {
                              "id": 326,
                              "city": "Ijebu-Jesa"
                          },
                          {
                              "id": 327,
                              "city": "Ikire"
                          },
                          {
                              "id": 328,
                              "city": "Ikirun"
                          },
                          {
                              "id": 329,
                              "city": "Ila Orangun"
                          },
                          {
                              "id": 330,
                              "city": "Ile-Ife"
                          },
                          {
                              "id": 331,
                              "city": "Ilesa"
                          },
                          {
                              "id": 332,
                              "city": "Ilobu"
                          },
                          {
                              "id": 333,
                              "city": "Inisa"
                          },
                          {
                              "id": 334,
                              "city": "Iwo"
                          },
                          {
                              "id": 335,
                              "city": "Modakeke"
                          },
                          {
                              "id": 336,
                              "city": "Oke Mesi"
                          },
                          {
                              "id": 337,
                              "city": "Olupona"
                          },
                          {
                              "id": 338,
                              "city": "Osogbo"
                          },
                          {
                              "id": 339,
                              "city": "Otan Ayegbaju"
                          },
                          {
                              "id": 340,
                              "city": "Oyan"
                          }
                      ]
                  },
                  {
                      "id": 31,
                      "state": "Oyo State",
                      "cities": [
                          {
                              "id": 341,
                              "city": "Ago Are"
                          },
                          {
                              "id": 342,
                              "city": "Alapa"
                          },
                          {
                              "id": 343,
                              "city": "Fiditi"
                          },
                          {
                              "id": 344,
                              "city": "Ibadan"
                          },
                          {
                              "id": 345,
                              "city": "Igbeti"
                          },
                          {
                              "id": 346,
                              "city": "Igbo-Ora"
                          },
                          {
                              "id": 347,
                              "city": "Igboho"
                          },
                          {
                              "id": 348,
                              "city": "Kisi"
                          },
                          {
                              "id": 349,
                              "city": "Lalupon"
                          },
                          {
                              "id": 350,
                              "city": "Ogbomoso"
                          },
                          {
                              "id": 351,
                              "city": "Okeho"
                          },
                          {
                              "id": 352,
                              "city": "Orita Eruwa"
                          },
                          {
                              "id": 353,
                              "city": "Oyo"
                          },
                          {
                              "id": 354,
                              "city": "Saki"
                          }
                      ]
                  },
                  {
                      "id": 32,
                      "state": "Plateau State",
                      "cities": [
                          {
                              "id": 355,
                              "city": "Amper"
                          },
                          {
                              "id": 356,
                              "city": "Bukuru"
                          },
                          {
                              "id": 357,
                              "city": "Dengi"
                          },
                          {
                              "id": 358,
                              "city": "Jos"
                          },
                          {
                              "id": 359,
                              "city": "Kwolla"
                          },
                          {
                              "id": 360,
                              "city": "Langtang"
                          },
                          {
                              "id": 361,
                              "city": "Pankshin"
                          },
                          {
                              "id": 362,
                              "city": "Panyam"
                          },
                          {
                              "id": 363,
                              "city": "Vom"
                          },
                          {
                              "id": 364,
                              "city": "Yelwa"
                          }
                      ]
                  },
                  {
                      "id": 33,
                      "state": "Sokoto State",
                      "cities": [
                          {
                              "id": 365,
                              "city": "Binji"
                          },
                          {
                              "id": 366,
                              "city": "Dange"
                          },
                          {
                              "id": 367,
                              "city": "Gandi"
                          },
                          {
                              "id": 368,
                              "city": "Goronyo"
                          },
                          {
                              "id": 369,
                              "city": "Gwadabawa"
                          },
                          {
                              "id": 370,
                              "city": "Illela"
                          },
                          {
                              "id": 371,
                              "city": "Rabah"
                          },
                          {
                              "id": 372,
                              "city": "Sokoto"
                          },
                          {
                              "id": 373,
                              "city": "Tambuwal"
                          },
                          {
                              "id": 374,
                              "city": "Wurno"
                          }
                      ]
                  },
                  {
                      "id": 34,
                      "state": "Taraba State",
                      "cities": [
                          {
                              "id": 375,
                              "city": "Baissa"
                          },
                          {
                              "id": 376,
                              "city": "Beli"
                          },
                          {
                              "id": 377,
                              "city": "Gassol"
                          },
                          {
                              "id": 378,
                              "city": "Gembu"
                          },
                          {
                              "id": 379,
                              "city": "Ibi"
                          },
                          {
                              "id": 380,
                              "city": "Jalingo"
                          },
                          {
                              "id": 381,
                              "city": "Lau"
                          },
                          {
                              "id": 382,
                              "city": "Mutum Biyu"
                          },
                          {
                              "id": 383,
                              "city": "Riti"
                          },
                          {
                              "id": 384,
                              "city": "Wukari"
                          }
                      ]
                  },
                  {
                      "id": 35,
                      "state": "Yobe State",
                      "cities": [
                          {
                              "id": 385,
                              "city": "Damaturu"
                          },
                          {
                              "id": 386,
                              "city": "Dankalwa"
                          },
                          {
                              "id": 387,
                              "city": "Dapchi"
                          },
                          {
                              "id": 388,
                              "city": "Daura"
                          },
                          {
                              "id": 389,
                              "city": "Fika"
                          },
                          {
                              "id": 390,
                              "city": "Gashua"
                          },
                          {
                              "id": 391,
                              "city": "Geidam"
                          },
                          {
                              "id": 392,
                              "city": "Goniri"
                          },
                          {
                              "id": 393,
                              "city": "Gorgoram"
                          },
                          {
                              "id": 394,
                              "city": "Gujba"
                          },
                          {
                              "id": 395,
                              "city": "Gwio Kura"
                          },
                          {
                              "id": 396,
                              "city": "Kumagunnam"
                          },
                          {
                              "id": 397,
                              "city": "Lajere"
                          },
                          {
                              "id": 398,
                              "city": "Machina"
                          },
                          {
                              "id": 399,
                              "city": "Nguru"
                          },
                          {
                              "id": 400,
                              "city": "Potiskum"
                          }
                      ]
                  },
                  {
                      "id": 36,
                      "state": "Zamfara State",
                      "cities": [
                          {
                              "id": 401,
                              "city": "Anka"
                          },
                          {
                              "id": 402,
                              "city": "Dan Sadau"
                          },
                          {
                              "id": 403,
                              "city": "Gummi"
                          },
                          {
                              "id": 404,
                              "city": "Gusau"
                          },
                          {
                              "id": 405,
                              "city": "Kaura Namoda"
                          },
                          {
                              "id": 406,
                              "city": "Kwatarkwashi"
                          },
                          {
                              "id": 407,
                              "city": "Maru"
                          },
                          {
                              "id": 408,
                              "city": "Moriki"
                          },
                          {
                              "id": 409,
                              "city": "Sauri"
                          },
                          {
                              "id": 410,
                              "city": "Tsafe"
                          }
                      ]
                  }
              ]
          }
      ],
      "message": "Ok",
      "success": true
  }';
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function createState($state_name, $country_id)
    {
        $state = State::where('state_name', $state_name)
            ->where('country_id', $country_id)->first();
        if (!isset($state)) {
            $state = State::create([
                'state_name' => $state_name,
                'status' => $this->getResourceActiveId(),
                'country_id' => $country_id
            ]);
        }
        return $state;
    }

    protected function createCity($city_name, $state_id)
    {
        $city = City::where('city_name', $city_name)
            ->where('state_id', $state_id)->first();
        if (!isset($city)) {
            $city = City::create([
                'city_name' => $city_name,
                'state_id' => $state_id,
                'status' => $this->getResourceActiveId()
            ]);
        }
        return $city;
    }

    protected function populateLocations($data)
    {
        if(isset($data) && count($data) > 0){
            foreach($data as $country_data){
                $country_model = Country::where('country_code',$country_data->code)->first();
                if(isset($country_model)){
                    DB::transaction(function()use($country_model,$country_data){
                        $states = $country_data->states;
                        if(isset($states) && count($states) > 0){
                            foreach($states as $state_item){
                                $state_model = $this->createState($state_item->state,$country_model->id);
                                $cities = $state_item->cities;
                                if(isset($cities) && count($cities) > 0){
                                    foreach($cities as $city_item){
                                        $this->createCity($city_item->city,$state_model->id);
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    }

    protected function getLocationsData()
    {
        $payload = json_decode($this->list);
        $data = $payload->data;
        return $data;
    }

    public function execute()
    {
        try {
            $data = $this->getLocationsData();
            $this->populateLocations($data);
            return $this->successMessage('Locations populated successfully.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
