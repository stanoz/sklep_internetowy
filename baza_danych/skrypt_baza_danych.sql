
CREATE TABLE Adres (
    ID_adres int  NOT NULL AUTO_INCREMENT,
    id_uzytkownik int  NOT NULL,
    miasto varchar(50)  NOT NULL,
    ulica varchar(50)  NOT NULL,
    kod_pocztowy varchar(6)  NOT NULL,
    nr_mieszkania int  NULL,
    nr_domu int  NULL,
    CONSTRAINT Adres_pk PRIMARY KEY (ID_adres)
);


CREATE TABLE Dane_kontaktowe (
    ID_dane_kontaktowe int  NOT NULL AUTO_INCREMENT,
    id_uzytkownik int  NOT NULL,
    nr_telefonu varchar(12)  NOT NULL,
    adres_email varchar(60)  NOT NULL,
    CONSTRAINT Dane_kontaktowe_pk PRIMARY KEY (ID_dane_kontaktowe)
);


CREATE TABLE Kategoria (
    ID_kategoria int  NOT NULL,
    kategoria varchar(50)  NOT NULL,
    CONSTRAINT Kategoria_pk PRIMARY KEY (ID_kategoria)
);


CREATE TABLE Opinie (
    ID_opinia int  NOT NULL AUTO_INCREMENT,
    opinia varchar(255)  NOT NULL,
    ocena int  NOT NULL,
    id_uzytkownik int  NOT NULL,
    data_wystawienia date  NOT NULL,
    id_produkt int  NOT NULL,
    CONSTRAINT Opinie_pk PRIMARY KEY (ID_opinia)
);


CREATE TABLE Produkty (
    ID_produkt int  NOT NULL AUTO_INCREMENT,
    cena decimal(8,2)  NULL,
    zdjecie varchar(50)  NOT NULL,
    id_kategoria int  NOT NULL,
    ilosc int  NOT NULL,
    nazwa varchar(100)  NOT NULL,
    opis varchar(255)  NULL,
    CONSTRAINT Produkty_pk PRIMARY KEY (ID_produkt)
);


CREATE TABLE Reklamacje (
    ID_reklamacja int  NOT NULL AUTO_INCREMENT,
    id_uzytkownik int  NOT NULL,
    id_zamowienia int  NOT NULL,
    id_produkt int  NOT NULL,
    tresc varchar(255)  NOT NULL,
    data_zlozenia date  NOT NULL,
    CONSTRAINT Reklamacje_pk PRIMARY KEY (ID_reklamacja)
);


CREATE TABLE Typ_uzytkownika (
    ID_typ_uzytkownika int  NOT NULL AUTO_INCREMENT,
    typ varchar(10)  NOT NULL,
    CONSTRAINT Typ_uzytkownika_pk PRIMARY KEY (ID_typ_uzytkownika)
);


CREATE TABLE Uzytkownicy (
    ID_uzytkownik int  NOT NULL AUTO_INCREMENT,
    imie varchar(30)  NOT NULL,
    nazwisko varchar(50)  NOT NULL,
    adres_email varchar(60)  NOT NULL,
    haslo varchar(255)  NOT NULL,
    id_typ_uzytkownika int  NOT NULL,
    CONSTRAINT Uzytkownicy_pk PRIMARY KEY (ID_uzytkownik)
);


CREATE TABLE Zamowienia (
    ID_zamowienia int  NOT NULL AUTO_INCREMENT,
    id_uzytkownik int  NOT NULL,
    id_adres int  NOT NULL,
    sposob_platnosci varchar(30)  NOT NULL,
    wartosc_zamowienia decimal(10,2)  NOT NULL,
    id_dane_kontaktowe int  NOT NULL,
    data_zlozenia datetime  NOT NULL,
    stan varchar(25)  NOT NULL,
    czy_rabat bool  NOT NULL,
    CONSTRAINT Zamowienia_pk PRIMARY KEY (ID_zamowienia)
);


CREATE TABLE Zamowione_produkty (
    ID_zamowione_produkty int  NOT NULL AUTO_INCREMENT,
    id_produktu int  NOT NULL,
    ilosc_sztuk int  NOT NULL,
    id_zamowienia int  NOT NULL,
    CONSTRAINT Zamowione_produkty_pk PRIMARY KEY (ID_zamowione_produkty)
);


CREATE TABLE rabaty (
    ID_rabat int  NOT NULL AUTO_INCREMENT,
    tresc varchar(50)  NOT NULL,
    CONSTRAINT rabaty_pk PRIMARY KEY (ID_rabat)
);


ALTER TABLE Produkty ADD CONSTRAINT Kategoria_Produkty FOREIGN KEY Kategoria_Produkty (id_kategoria)
    REFERENCES Kategoria (ID_kategoria);


ALTER TABLE Opinie ADD CONSTRAINT Opinie_Uzytkownicy FOREIGN KEY Opinie_Uzytkownicy (id_uzytkownik)
    REFERENCES Uzytkownicy (ID_uzytkownik);


ALTER TABLE Opinie ADD CONSTRAINT Produkty_Opinie FOREIGN KEY Produkty_Opinie (id_produkt)
    REFERENCES Produkty (ID_produkt);


ALTER TABLE Reklamacje ADD CONSTRAINT Produkty_Reklamacje FOREIGN KEY Produkty_Reklamacje (id_produkt)
    REFERENCES Produkty (ID_produkt);


ALTER TABLE Zamowione_produkty ADD CONSTRAINT Produkty_Zamowione_produkty FOREIGN KEY Produkty_Zamowione_produkty (id_produktu)
    REFERENCES Produkty (ID_produkt);


ALTER TABLE Adres ADD CONSTRAINT Uzytkownicy_Adres FOREIGN KEY Uzytkownicy_Adres (id_uzytkownik)
    REFERENCES Uzytkownicy (ID_uzytkownik);


ALTER TABLE Dane_kontaktowe ADD CONSTRAINT Uzytkownicy_Dane_kontaktowe FOREIGN KEY Uzytkownicy_Dane_kontaktowe (id_uzytkownik)
    REFERENCES Uzytkownicy (ID_uzytkownik);


ALTER TABLE Reklamacje ADD CONSTRAINT Uzytkownicy_Reklamacje FOREIGN KEY Uzytkownicy_Reklamacje (id_uzytkownik)
    REFERENCES Uzytkownicy (ID_uzytkownik);


ALTER TABLE Uzytkownicy ADD CONSTRAINT Uzytkownicy_Typ_uzytkownika FOREIGN KEY Uzytkownicy_Typ_uzytkownika (id_typ_uzytkownika)
    REFERENCES Typ_uzytkownika (ID_typ_uzytkownika);


ALTER TABLE Zamowienia ADD CONSTRAINT Uzytkownicy_Zamowienia FOREIGN KEY Uzytkownicy_Zamowienia (id_uzytkownik)
    REFERENCES Uzytkownicy (ID_uzytkownik);


ALTER TABLE Zamowienia ADD CONSTRAINT Zamowienia_Adres FOREIGN KEY Zamowienia_Adres (id_adres)
    REFERENCES Adres (ID_adres);


ALTER TABLE Zamowienia ADD CONSTRAINT Zamowienia_Dane_kontaktowe FOREIGN KEY Zamowienia_Dane_kontaktowe (id_dane_kontaktowe)
    REFERENCES Dane_kontaktowe (ID_dane_kontaktowe);


ALTER TABLE Reklamacje ADD CONSTRAINT Zamowienia_Reklamacje FOREIGN KEY Zamowienia_Reklamacje (id_zamowienia)
    REFERENCES Zamowienia (ID_zamowienia);


ALTER TABLE Zamowione_produkty ADD CONSTRAINT Zamowienia_Zamowione_produkty FOREIGN KEY Zamowienia_Zamowione_produkty (id_zamowienia)
    REFERENCES Zamowienia (ID_zamowienia);


