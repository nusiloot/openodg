#!/bin/bash

. bin/config.inc

echo "Import des identités"

if ! test "$1"; then
    echo "Paramêtre de récupération des données obligatoire";
    exit 1;
fi

SYMFODIR=$(pwd);

LOGDATE=$SYMFODIR/$(date +%Y%m%d%H%M%S_import_data.log)

if [ ! -d "$TMPDIR/ODGRHONE_IDENTITES_DATA" ]; then

scp $1 $TMPDIR/ODGRHONE_IDENTITES_DATA.xml.gz

echo "Dézippage";
rm -rf $TMPDIR/ODGRHONE_IDENTITES_DATA 2>/dev/null
mkdir $TMPDIR/ODGRHONE_IDENTITES_DATA 2> /dev/null
mkdir $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA 2> /dev/null
gunzip $TMPDIR/ODGRHONE_IDENTITES_DATA.xml.gz
mv $TMPDIR/ODGRHONE_IDENTITES_DATA.xml $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.xml


 cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.xml | sed -e 's|<b:Identite_Identite>|\n<b:Identite_Identite>|g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA_N.xml

## ici retirer le head

# cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA_N.xml | grep -E '^<b:Identite_Identite>' | sed -r 's/(.*)<b:CleIdentite>([0-9]+)<\/b:CleIdentite>(.*)/\2###\1<b:CleIdentite>\2<\/b:CleIdentite>\3/g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.tmp.xml

cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA_N.xml | grep -E '^<b:Identite_Identite>' | sed -r 's/(.*)<b:CleIdentite>([0-9]+)<\/b:CleIdentite>(.*)/\2###\1<b:CleIdentite>\2<\/b:CleIdentite>\3/g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.tmp.xml

echo "Création des xml entités";
###
### DANS CETTE BOUCLE ON CHERCHE LES DEFINITIONS DE GROUPES
###
cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.tmp.xml | grep -iv "VIRER[^NE]" | grep -E "(<b:Siret>.+</b:Siret>|<b:Evv>.+</b:Evv>|<b:NumPPM>.+</b:NumPPM>)" | while read xml
do
  IDFIC=$(echo $xml | sed -r 's/([0-9]+)###(.*)/\1/g')
  echo $xml | sed -r 's/([0-9]+)###(.*)/\2/g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/evvSiret_$IDFIC.xml
done


cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.tmp.xml | grep -iv "VIRER[^NE]" | grep -v "<b:Siret>" | grep -v "<b:Evv>" | grep -v "<b:NumPPM>" | grep "<b:Groupes><b:Identite_Groupe>" | while read xml
do
  IDFIC=$(echo $xml | sed -r 's/([0-9]+)###(.*)/\1/g')
  echo $xml | sed -r 's/([0-9]+)###(.*)/\2/g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/autres_groupes_$IDFIC.xml
done

cat $TMPDIR/ODGRHONE_IDENTITES_DATA/ODGRHONE_IDENTITES_DATA.tmp.xml | grep -iv "VIRER[^NE]" | grep -v "<b:Siret>" | grep -v "<b:Evv>" | grep -v "<b:NumPPM>" | grep -v "<b:Groupes><b:Identite_Groupe>" | while read xml
do
  IDFIC=$(echo $xml | sed -r 's/([0-9]+)###(.*)/\1/g')
  echo $xml | sed -r 's/([0-9]+)###(.*)/\2/g' > $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/autres_nogroupes_$IDFIC.xml
done
fi


echo "Création des entités de type Sociétés (Présence d'un Evv ou d'un Siret ou d'un PPM)"
for path in $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/evvSiret_*.xml ; do
  php symfony import:entite-from-xml --trace $path --application="declaration"
done

echo "Autres entités ayant des groupes";
for path in $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/autres_groupes_*.xml ; do
  php symfony import:entite-from-xml --trace $path --application="declaration"
done

echo "Autres entités n'ayant pas de groupes";
for path in $TMPDIR/ODGRHONE_IDENTITES_DATA/IDENTITES_DATA/autres_nogroupes_*.xml ; do
  php symfony import:entite-from-xml --trace $path --application="declaration"
done
