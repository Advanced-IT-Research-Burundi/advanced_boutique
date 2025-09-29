import mysql.connector
import pandas as pd

mydb = mysql.connector.connect(
  host="localhost",
  user="jean",
  password="lion",
  database="advanced_boutique_test"
)

all_sheets_data = pd.read_excel('prod.xlsx', sheet_name=None)

listeProduits = []
for sheet_name, df in all_sheets_data.items():
    print(f"Data from sheet: {sheet_name}")
    for index, row in df.iterrows():
      # row est une Series -> tu peux accéder par nom de colonne
      # affiche la ligne comme un dictionnaire
      ligne = row.to_dict()
      
      listeProduits.append((
        ligne.get("Code article", "-"),
        ligne.get("Nom article", "-"),
        ligne.get("PVHT", "-"),
        ligne.get("PVTTC", "-")
      ))
    """    print(ligne.get("Code article", "-")  , "\t| " ,  str(ligne.get("Nom article", "-")), "\t| " , ligne.get("PVHT", "-") , "\t| " , ligne.get("PVTTC", "-") )
          print("-" * 80) """
      

        # Exemple d’accès : row["NomColonne"]


print("Total produits: " , len(listeProduits))
#print(listeProduits)  # Affiche les 5 premiers produits pour vérification

mycursor = mydb.cursor()

sql = "INSERT INTO produits_tmps (code, designation, PVHT, PVTTC) VALUES (%s, %s, %s, %s)"

mycursor.executemany(sql, listeProduits)

mydb.commit()

print(mycursor.rowcount, "produits ajoutés.")