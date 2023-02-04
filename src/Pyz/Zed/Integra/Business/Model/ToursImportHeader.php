<?php
/**
 * Durst - project - ToursImportHeader.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 13:13
 */

namespace Pyz\Zed\Integra\Business\Model;


interface ToursImportHeader
{
    public const HEADER_RECEIPT_DID = 'BELEGDID';
    public const HEADER_EVENT_NO = 'NRVORGANG';
    public const HEADER_RECEIPT_TYPE_NO = 'NRBELEGTYP';
    public const HEADER_RECEIPT_NO = 'NRBELEG';
    public const HEADER_CUSTOMER_NO = 'NRKD';
    public const HEADER_CUSTOMER_SHIPPING_NO = 'NRKDVERSAND';
    public const HEADER_DELIVERY_DATE = 'WUNSCHTERMIN';
    public const HEADER_SHIPPING_TYPE_NO = 'NRVERSANDART';
    public const HEADER_TOUR_NO = 'NRTOUR';
    public const HEADER_TOUR_SEQUENCE = 'NRTOURFOLGE';
    public const HEADER_TOUR_TRIP_NO = 'NRTOURFAHRT';
    public const HEADER_NR_TOURLIEF = 'NR_TOURLIEF';
    public const HEADER_PAYMENT_TYPE_NO = 'NRZAHLART';
    public const HEADER_ZKD_NO = 'NRZKD';
    public const HEADER_CURRENCY = 'WHRG';
    public const HEADER_PROJECT_NO = 'NRPROJEKT';
    public const HEADER_EXTERNAL_RECEIPT_NO = 'FREMDBELEGNR';
    public const HEADER_EXTERNAL_RECEIPT_DATE = 'FREMDBELEGDAT';
    public const HEADER_KEYWORD = 'STICHWORT';
    public const HEADER_ABRGEMPF_NO = 'NRABRGEMPF';
    public const HEADER_DIFF_DEBITOR_NO = 'NRABWDEBITOR';
    public const HEADER_NET_GROSS = 'NETBRUT';
    public const HEADER_LANDKZSTEUER = 'LANDKZSTEUER';
    public const HEADER_TAX_NO = 'USTIDNR';
    public const HEADER_EVENT_TYPE_NO = 'NRVORGART';
    public const HEADER_EVENT_TYPE = 'VORGANGTYP';
    public const HEADER_ERSTEDITID = 'ERSTEDITID';
    public const HEADER_VALUTA_DAYS = 'VALUTATAGE';
    public const HEADER_VALUTA_DATE = 'VALUTADATUM';
    public const HEADER_POSITION_DID = 'POSITIONDID';
    public const HEADER_POSITION_TYPE = 'POSITIONSART';
    public const HEADER_NO = 'NUMMER';
    public const HEADER_DESCRIPTION = 'BEZEICHNUNG';
    public const HEADER_LEVEL = 'STUFE';
    public const HEADER_LEVEL_TYPE = 'STUFEART';
    public const HEADER_INTERNAL_POSITION_NO = 'INTERNPOSNR';
    public const HEADER_VALID = 'GUELTIG';
    public const HEADER_SPDRUCK = 'SPDRUCK';
    public const HEADER_KZDRUCK = 'KZDRUCK';
    public const HEADER_ABSPOS = 'ABSPOS';
    public const HEADER_KZARTSPEZ = 'KZARTSPEZ';
    public const HEADER_TAX_RATE = 'PROZENTSTEUER';
    public const HEADER_RECEIPT_COUNT = 'BELEGMENGE';
    public const HEADER_AMOUNT = 'BETRAG';
    public const HEADER_TAX_AMOUNT = 'BETRAGSTEUER';
    public const HEADER_FWBETRAG = 'FWBETRAG';
    public const HEADER_FWBETRAGSTEUER = 'FWBETRAGSTEUER';
    public const HEADER_BETRAGSKOFAEHIG = 'BETRAGSKOFAEHIG';
    public const HEADER_FWBETRAGSKOFAEHIG = 'FWBETRAGSKOFAEHIG';
    public const HEADER_WARENEINSATZ = 'WARENEINSATZ';
    public const HEADER_DECKUNGSBEITRAG = 'DECKUNGSBEITRAG';
    public const HEADER_BASISPREISDBRECHN = 'BASISPREISDBRECHN';
    public const HEADER_NACHLASSSUMME = 'NACHLASSSUMME';
    public const HEADER_FWNACHLASSSUMME = 'FWNACHLASSSUMME';
    public const HEADER_NACHLASSSUMMEBEL = 'NACHLASSSUMMEBEL';
    public const HEADER_FWNACHLASSSUMMEBEL = 'FWNACHLASSSUMMEBEL';
    public const HEADER_NACHLASSDBNEUTRAL = 'NACHLASSDBNEUTRAL';
    public const HEADER_WEIGHT = 'GEWICHT';
    public const HEADER_VOLUME = 'VOLUMEN';
    public const HEADER_BARREL_COUNT = 'ANZFASS';
    public const HEADER_CASE_COUNT = 'ANZKAST';
    public const HEADER_BOX_COUNT = 'ANZKARTON';
    public const HEADER_OTHER_COUNT = 'ANZSONST';
    public const HEADER_PACKAGING_COUNT = 'MENGEVERPEINH';
    public const HEADER_PACKAGING_PRICE = 'PREISVERPEINH';
    public const HEADER_LFDNRVERPEINH = 'LFDNRVERPEINH';
    public const HEADER_MENGENEINTRANSPEH = 'MENGENEINTRANSPEH';
    public const HEADER_LFDVERPTRANSPORT = 'LFDVERPTRANSPORT';
    public const HEADER_MENGENEINGEBINHALT = 'MENGENEINGEBINHALT';
    public const HEADER_LFDNRGEBINHALT = 'LFDNRGEBINHALT';
    public const HEADER_ANZTRANSPEINHDEZ = 'ANZTRANSPEINHDEZ';
    public const HEADER_MENGENEINLAGEREH = 'MENGENEINLAGEREH';
    public const HEADER_RUECKSTANDST = 'RUECKSTANDST';
    public const HEADER_TIME_SLOT_START = 'VONUHR1';
    public const HEADER_TIME_SLOT_END = 'BISUHR1';
    public const HEADER_EXTERNNUMMER= 'EXTERNNUMMER';
    public const HEADER_NRME = 'NRME';

    public const HEADER = [
        self::HEADER_RECEIPT_DID,
        self::HEADER_EVENT_NO,
        self::HEADER_RECEIPT_TYPE_NO,
        self::HEADER_RECEIPT_NO,
        self::HEADER_CUSTOMER_NO,
        self::HEADER_CUSTOMER_SHIPPING_NO,
        self::HEADER_DELIVERY_DATE,
        self::HEADER_SHIPPING_TYPE_NO,
        self::HEADER_TOUR_NO,
        self::HEADER_TOUR_SEQUENCE,
        self::HEADER_TOUR_TRIP_NO,
        self::HEADER_NR_TOURLIEF,
        self::HEADER_PAYMENT_TYPE_NO,
        self::HEADER_ZKD_NO,
        self::HEADER_CURRENCY,
        self::HEADER_PROJECT_NO,
        self::HEADER_EXTERNAL_RECEIPT_NO,
        self::HEADER_EXTERNAL_RECEIPT_DATE,
        self::HEADER_KEYWORD,
        self::HEADER_ABRGEMPF_NO,
        self::HEADER_DIFF_DEBITOR_NO,
        self::HEADER_NET_GROSS,
        self::HEADER_LANDKZSTEUER,
        self::HEADER_TAX_NO,
        self::HEADER_EVENT_TYPE_NO,
        self::HEADER_EVENT_TYPE,
        self::HEADER_ERSTEDITID,
        self::HEADER_VALUTA_DAYS,
        self::HEADER_VALUTA_DATE,
        self::HEADER_POSITION_DID,
        self::HEADER_POSITION_TYPE,
        self::HEADER_NO,
        self::HEADER_DESCRIPTION,
        self::HEADER_LEVEL,
        self::HEADER_LEVEL_TYPE,
        self::HEADER_INTERNAL_POSITION_NO,
        self::HEADER_VALID,
        self::HEADER_SPDRUCK,
        self::HEADER_KZDRUCK,
        self::HEADER_ABSPOS,
        self::HEADER_KZARTSPEZ,
        self::HEADER_TAX_RATE,
        self::HEADER_RECEIPT_COUNT,
        self::HEADER_AMOUNT,
        self::HEADER_TAX_AMOUNT,
        self::HEADER_FWBETRAG,
        self::HEADER_FWBETRAGSTEUER,
        self::HEADER_BETRAGSKOFAEHIG,
        self::HEADER_FWBETRAGSKOFAEHIG,
        self::HEADER_WARENEINSATZ,
        self::HEADER_DECKUNGSBEITRAG,
        self::HEADER_BASISPREISDBRECHN,
        self::HEADER_NACHLASSSUMME,
        self::HEADER_FWNACHLASSSUMME,
        self::HEADER_NACHLASSSUMMEBEL,
        self::HEADER_FWNACHLASSSUMMEBEL,
        self::HEADER_NACHLASSDBNEUTRAL,
        self::HEADER_WEIGHT,
        self::HEADER_VOLUME,
        self::HEADER_BARREL_COUNT,
        self::HEADER_CASE_COUNT,
        self::HEADER_BOX_COUNT,
        self::HEADER_OTHER_COUNT,
        self::HEADER_PACKAGING_COUNT,
        self::HEADER_PACKAGING_PRICE,
        self::HEADER_LFDNRVERPEINH,
        self::HEADER_MENGENEINTRANSPEH,
        self::HEADER_LFDVERPTRANSPORT,
        self::HEADER_MENGENEINGEBINHALT,
        self::HEADER_LFDNRGEBINHALT,
        self::HEADER_ANZTRANSPEINHDEZ,
        self::HEADER_MENGENEINLAGEREH,
        self::HEADER_RUECKSTANDST,
        self::HEADER_TIME_SLOT_START,
        self::HEADER_TIME_SLOT_END,
        self::HEADER_EXTERNNUMMER,
        self::HEADER_NRME,
    ];
}
