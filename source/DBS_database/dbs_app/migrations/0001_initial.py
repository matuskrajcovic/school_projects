from django.db import migrations, connection

def apply(apps, schema_editor):

    # creates new table companies
    q1 = "CREATE TABLE ov.companies ("
    q1 += "cin BIGINT NOT NULL PRIMARY KEY, "
    q1 += "name VARCHAR, "
    q1 += "br_section VARCHAR, "
    q1 += "address_line VARCHAR, "
    q1 += "last_update TIMESTAMP without time zone, "
    q1 += "created_at TIMESTAMP without time zone, "
    q1 += "updated_at TIMESTAMP without time zone);"
    
    # inserts data to company table, for each of the 5 source tables
    q2 =  "INSERT INTO ov.companies ("
    q2 +=   "SELECT cin, corporate_body_name, {0}, {1}, updated_at AS last_update, "
    q2 +=     "CURRENT_TIMESTAMP::timestamp without time zone AS created_at, "
    q2 +=     "CURRENT_TIMESTAMP::timestamp without time zone AS updated_at FROM ("
    q2 +=      "SELECT *, ROW_NUMBER() OVER (PARTITION BY cin ORDER BY updated_at DESC) AS row_num "
    q2 +=      "FROM ov.{2} WHERE cin IS NOT NULL) AS src "
    q2 +=   "WHERE row_num = 1) "
    q2 += "ON CONFLICT DO NOTHING;"
    
    # adds a foreign key column to all 5 tables
    q3 = "ALTER TABLE ov.{0} ADD COLUMN company_id BIGINT REFERENCES ov.companies(cin);"
    
    # sets all values equal to cin number
    q4 = "UPDATE ov.{0} SET company_id = cin;"
    
    # index on foreign key in table podanie_issues to speed up selections
    q5 = "CREATE INDEX company_id_index ON ov.or_podanie_issues (company_id);"
    
    tables = ["or_podanie_issues", "likvidator_issues", "konkurz_vyrovnanie_issues", "znizenie_imania_issues", "konkurz_restrukturalizacia_actors"]
    
    # each table has different structur (br_section and address_line columns)
    has_br = "br_section"
    no_br = "''"
    has_address = "address_line"
    no_address = "concat(street, ', ', postal_code, ' ', city)"

    # execute the queries with given parameters
    with connection.cursor() as cursor:
        cursor.execute(q1)
        cursor.execute(q2.format(has_br, has_address, tables[0]))
        cursor.execute(q2.format(has_br, no_address, tables[1]))
        cursor.execute(q2.format(no_br, no_address, tables[2]))
        cursor.execute(q2.format(has_br, no_address, tables[3]))
        cursor.execute(q2.format(no_br, no_address, tables[4]))
        for table in tables:
            cursor.execute(q3.format(table))
            cursor.execute(q4.format(table))
        cursor.execute(q5)

def reverse(apps, schema_editor):

    tables = ["or_podanie_issues", "likvidator_issues", "konkurz_vyrovnanie_issues", "znizenie_imania_issues", "konkurz_restrukturalizacia_actors"]
    
    q1 = "DROP INDEX ov.company_id_index;"
    q2 = "ALTER TABLE ov.{0} DROP COLUMN company_id;"
    q3 = "DROP TABLE ov.companies;"
    
    with connection.cursor() as cursor:
        cursor.execute(q1)
        for table in tables:
            cursor.execute(q2.format(table))
        cursor.execute(q3)


class Migration(migrations.Migration):
    
    dependencies = [
    ]

    operations = [
        migrations.RunPython(apply, reverse_code=reverse)
    ]	


# -------------------------------------------------------------
# nechutny sposob cez jednu query na vsetkych 5 tabuliek

"""
q2 =  "INSERT INTO ov.companies ("
q2 +=  "SELECT cin, corporate_body_name, br_section, "
q2 +=  "CASE WHEN address_line = '' THEN concat(street, ', ', postal_code, ' ', city) ELSE address_line END AS address_line, "
q2 +=  "last_update, "
q2 +=  "CURRENT_TIMESTAMP::timestamp without time zone AS created_at, CURRENT_TIMESTAMP::timestamp without time zone AS updated_at "
q2 +=  "FROM "
q2 +=  "(SELECT cin, corporate_body_name, br_section, address_line, street, postal_code, city, last_update "
q2 +=   "FROM "
q2 +=   "(SELECT *, max(updated_at) OVER (PARTITION BY cin) AS last_update "
q2 +=    "FROM ov.or_podanie_issues WHERE cin IS NOT NULL) AS src "
q2 +=   "WHERE last_update = src.updated_at "
q2 +=   "UNION ALL "
q2 +=   "SELECT cin, corporate_body_name, br_section, '' AS address_line, street, postal_code, city, last_update "
q2 +=   "FROM "
q2 +=   "(SELECT *, max(updated_at) OVER (PARTITION BY cin) AS last_update "
q2 +=    "FROM ov.likvidator_issues WHERE cin IS NOT NULL) AS src "
q2 +=   "WHERE last_update = src.updated_at "
q2 +=   "UNION ALL "
q2 +=   "SELECT cin, corporate_body_name, '' AS br_section, '' AS address_line, street, postal_code, city, last_update "
q2 +=   "FROM "
q2 +=   "(SELECT *, max(updated_at) OVER (PARTITION BY cin) AS last_update "
q2 +=    "FROM ov.konkurz_vyrovnanie_issues WHERE cin IS NOT NULL) AS src "
q2 +=   "WHERE last_update = src.updated_at "
q2 +=   "UNION ALL "
q2 +=   "SELECT cin, corporate_body_name, br_section, '' AS address_line, street, postal_code, city, last_update "
q2 +=   "FROM "
q2 +=   "(SELECT *, max(updated_at) OVER (PARTITION BY cin) AS last_update "
q2 +=    "FROM ov.znizenie_imania_issues WHERE cin IS NOT NULL) AS src "
q2 +=   "WHERE last_update = src.updated_at "
q2 +=   "UNION ALL "
q2 +=   "SELECT cin, corporate_body_name, '' AS br_section, '' AS address_line, street, postal_code, city, last_update "
q2 +=   "FROM "
q2 +=   "(SELECT *, max(updated_at) OVER (PARTITION BY cin) AS last_update "
q2 +=    "FROM ov.konkurz_restrukturalizacia_actors WHERE cin IS NOT NULL) AS src "
q2 +=   "WHERE last_update = src.updated_at) "
q2 +=  "AS src_all) "
q2 += "ON CONFLICT DO NOTHING;"
"""    