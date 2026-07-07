<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

public function getAvatarlar() {

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://tedarik-api.pttavm.com/order/list',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "start": 0,
        "length": 100,
        "search": "",
        "filter": {
            "order_date": [
                {
                    "operator": ">=",
                    "value": "2024-11-05"
                },
                {
                    "operator": "<=",
                    "value": "2024-12-05"
                }
            ]
        },
        "order": "desc",
        "order_columns": "order_date",
        "search_columns": [
            "order_id",
            "user_name",
            "user_surname",
            "user_login",
            "telephone",
            "address"
        ]
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer l1wo-5C_4PW5ybEIcDbLT2mkMPpAHN7xCLxMkzYeM3P074ZoFIonmH_E8vDGelUhqwsBxt9RqOp6VaXxaELFUYJ8l898tH_WupwF2ALToxtn3A1Q8rlwnFhogX9VGZyAsSf2G4-YtPgd2eHsZR2BHEAA9lRs0E8huJzHmDnVnk0M0nfL-MzQIx98Cl3xIpWwkav6rVn1wzuAtcEz5dFyny3UOxg6A_1m5jYBEpQbXXYDnrWwMWlrsyOBxxLmvTO4LA2TqSj-QcW77drXVi2tzocH3J2dfJi-o4RDEH-dvF6819jMRvKkUG6TSRrfA6dX2SqylH7Ffc5rRP9d6ym5lc5isGFyhFQ_-luA25pq3hKqoeYFiY2OI1e8Id1Of_yA0YYn56ukCikQ_sToAas7lbSRiiCcqiqS6o21RNZ8O-GbPyCTKgHvNVCvUvCGmXVPkoe23aiXAcyp-IVrWrqJ1nU2KTeZG0cwdDfg3i4cn_Pc8sgNUD6LqPYedMyn7G-SMpWJgKy7jf4DcUh9PEDdCT2_C0hiwi7huzH-OmnmZFdX9VrBeXvHblqQiMEH-vPImaXOgOtW43I4cRhdu_bTzkJB7Kx89_7743DuVLzltCCv8Fe2easc3QnUy5NJb82JjqdcA9407NfQCQVwKphXLmibcx4UbNRlZ8tFGu1avdgfIOqBhKbGSxrl1dSTbgvVJRLXklvkiA1FrPzhhGDE8QOSDMantxmt0Q-DVtu-SGkx6Dn096PzquOts3MFeRhzNdmIjoxKkH6ERzo1wT1ON_uAxiTxJnALHLH5kAjZDiS2x-YCMVTPQM2JTnxseUaPeOQeDiJta3UT8cdJqFc4l3Tc02rAxz_kl3aZXUVuDe05yomEW_rogiyvS3vLeu4jyzmlvosBY6URZNWiE6fXFNU3fKVY9MtqifBqQXsYT1z7qPFCS5qcZA7NOewaCr8XyYrBsIizfImb_PLp89kRTEQcZpleZaV0cmUaOeXUTsTOa-1YV26bje8uZ6A3NRoWHBejDg5H3eZ1MtYuuQ-4GhQIsq3_pQhELeEVyJxAD4iyQlZbolVRXpMoO7EYBdGxVZBjMlL6u6LBoRNc4eTWFERQAdZRXEjSCiF0tRkMkAvytLl5ZdCjmqpJsJ95WNL-2IMXz9iGm_ahwBuWQqzKh29RKkL8eeADphIkd99Q2LbGblQ4z1AbOMStVxjv9ZbvFfqVFZM5ChiuFvL6MFZ-qF-TpoKelAT1tL-3oMYqLC3VHhcnLRBJcm4fX0F-JaDxkdGnXrrBEtEWls8IAoGIUZZYMr_cFukVUqgV4wpnuJ6OIZ7Hf8IQRiZ39PdJEyXqY3d2-ontQ6DKa_lp759xi0J0tQNcw5jA7nrlFRbYnUgyPwMKng4EbidrA8JWC8ZaAqCovTmFku36Pd-0OmrM'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}
