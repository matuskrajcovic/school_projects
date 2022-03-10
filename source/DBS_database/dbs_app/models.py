from django.db import models


class bulletin_issues(models.Model):
    
    year = models.IntegerField()
    number = models.IntegerField()
    published_at = models.DateTimeField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"bulletin_issues'
        unique_together = (('updated_at', 'id'), ('year', 'number'),)


class raw_issues(models.Model):
    
    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    file_name = models.TextField()
    content = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"raw_issues'
        unique_together = (('updated_at', 'id'),)


class companies(models.Model):

    cin = models.BigIntegerField(primary_key=True)
    name = models.TextField(blank=True, null=True)
    br_section = models.TextField(blank=True, null=True)
    address_line = models.TextField(blank=True, null=True)
    last_update = models.DateTimeField(blank=True, null=True)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"companies'


class konkurz_restrukturalizacia_actors(models.Model):
    
    corporate_body_name = models.TextField(blank=True, null=True)
    cin = models.BigIntegerField(blank=True, null=True)
    street = models.TextField(blank=True, null=True)
    building_number = models.TextField(blank=True, null=True)
    city = models.TextField(blank=True, null=True)
    postal_code = models.TextField(blank=True, null=True)
    country = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    company = models.ForeignKey(companies, models.DO_NOTHING, blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"konkurz_restrukturalizacia_actors'


class konkurz_restrukturalizacia_issues(models.Model):
    
    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    raw_issue = models.ForeignKey(raw_issues, models.DO_NOTHING)
    court_name = models.TextField()
    file_reference = models.TextField()
    ics = models.TextField()
    released_by = models.TextField()
    releaser_position = models.TextField(blank=True, null=True)
    sent_by = models.TextField(blank=True, null=True)
    released_date = models.DateField()
    debtor = models.ForeignKey(konkurz_restrukturalizacia_actors, models.DO_NOTHING, blank=True, null=True)
    kind = models.TextField()
    heading = models.TextField(blank=True, null=True)
    decision = models.TextField(blank=True, null=True)
    announcement = models.TextField(blank=True, null=True)
    advice = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"konkurz_restrukturalizacia_issues'
        unique_together = (('updated_at', 'id'),)


class konkurz_restrukturalizacia_proposings(models.Model):
    
    issue = models.ForeignKey(konkurz_restrukturalizacia_issues, models.DO_NOTHING)
    actor = models.ForeignKey(konkurz_restrukturalizacia_actors, models.DO_NOTHING)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"konkurz_restrukturalizacia_proposings'


class konkurz_vyrovnanie_issues(models.Model):
    
    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    raw_issue = models.ForeignKey(raw_issues, models.DO_NOTHING)
    court_code = models.TextField()
    court_name = models.TextField()
    file_reference = models.TextField()
    corporate_body_name = models.TextField()
    cin = models.BigIntegerField()
    street = models.TextField(blank=True, null=True)
    building_number = models.TextField(blank=True, null=True)
    city = models.TextField(blank=True, null=True)
    postal_code = models.TextField(blank=True, null=True)
    country = models.TextField(blank=True, null=True)
    kind_code = models.TextField()
    kind_name = models.TextField()
    announcement = models.TextField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    company = models.ForeignKey(companies, models.DO_NOTHING, blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"konkurz_vyrovnanie_issues'
        unique_together = (('updated_at', 'id'),)


class likvidator_issues(models.Model):
    
    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    raw_issue = models.ForeignKey(raw_issues, models.DO_NOTHING)
    legal_form_code = models.TextField()
    legal_form_name = models.TextField()
    corporate_body_name = models.TextField()
    cin = models.BigIntegerField()
    sid = models.TextField(blank=True, null=True)
    street = models.TextField()
    building_number = models.TextField()
    city = models.TextField()
    postal_code = models.TextField()
    country = models.TextField()
    in_business_register = models.BooleanField()
    br_insertion = models.TextField(blank=True, null=True)
    br_court_code = models.TextField(blank=True, null=True)
    br_court_name = models.TextField(blank=True, null=True)
    br_section = models.TextField(blank=True, null=True)
    other_registrar_name = models.TextField(blank=True, null=True)
    other_registration_number = models.TextField(blank=True, null=True)
    decision_based_on = models.TextField()
    decision_date = models.DateField()
    claim_term = models.TextField()
    liquidation_start_date = models.DateField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    debtee_legal_form_code = models.TextField(blank=True, null=True)
    debtee_legal_form_name = models.TextField(blank=True, null=True)
    company = models.ForeignKey(companies, models.DO_NOTHING, blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"likvidator_issues'
        unique_together = (('updated_at', 'id'),)


class or_podanie_issues(models.Model):

    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    raw_issue = models.ForeignKey(raw_issues, models.DO_NOTHING)
    br_mark = models.TextField()
    br_court_code = models.TextField()
    br_court_name = models.TextField()
    kind_code = models.TextField()
    kind_name = models.TextField()
    cin = models.BigIntegerField(blank=True, null=True)
    registration_date = models.DateField(blank=True, null=True)
    corporate_body_name = models.TextField(blank=True, null=True)
    br_section = models.TextField()
    br_insertion = models.TextField()
    text = models.TextField()
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    address_line = models.TextField(blank=True, null=True)
    street = models.TextField(blank=True, null=True)
    postal_code = models.TextField(blank=True, null=True)
    city = models.TextField(blank=True, null=True)
    company = models.ForeignKey(companies, models.DO_NOTHING, blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"or_podanie_issues'
        unique_together = (('updated_at', 'id'),)


class or_podanie_issue_documents(models.Model):
    
    or_podanie_issue = models.ForeignKey(or_podanie_issues, models.DO_NOTHING)
    name = models.TextField()
    delivery_date = models.DateField()
    ruz_deposit_date = models.DateField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"or_podanie_issue_documents'


class znizenie_imania_issues(models.Model):
    
    bulletin_issue = models.ForeignKey(bulletin_issues, models.DO_NOTHING)
    raw_issue = models.ForeignKey(raw_issues, models.DO_NOTHING)
    corporate_body_name = models.TextField()
    street = models.TextField(blank=True, null=True)
    building_number = models.TextField(blank=True, null=True)
    postal_code = models.TextField(blank=True, null=True)
    city = models.TextField(blank=True, null=True)
    country = models.TextField(blank=True, null=True)
    br_court_code = models.TextField()
    br_court_name = models.TextField()
    br_section = models.TextField()
    br_insertion = models.TextField()
    cin = models.BigIntegerField()
    decision_text = models.TextField(blank=True, null=True)
    decision_date = models.DateField(blank=True, null=True)
    equity_currency_code = models.TextField()
    old_equity_value = models.DecimalField(max_digits=12, decimal_places=2)
    new_equity_value = models.DecimalField(max_digits=12, decimal_places=2)
    resolution_store_date = models.DateField(blank=True, null=True)
    first_ov_released_date = models.DateField(blank=True, null=True)
    first_ov_released_number = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()
    company = models.ForeignKey(companies, models.DO_NOTHING, blank=True, null=True)

    class Meta:
        db_table = 'ov\".\"znizenie_imania_issues'
        unique_together = (('updated_at', 'id'),)


class znizenie_imania_ceos(models.Model):
    
    znizenie_imania_issue = models.ForeignKey(znizenie_imania_issues, models.DO_NOTHING)
    prefixes = models.TextField(blank=True, null=True)
    postfixes = models.TextField(blank=True, null=True)
    given_name = models.TextField(blank=True, null=True)
    family_name = models.TextField(blank=True, null=True)
    street = models.TextField(blank=True, null=True)
    building_number = models.TextField(blank=True, null=True)
    postal_code = models.TextField(blank=True, null=True)
    city = models.TextField(blank=True, null=True)
    country = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField()
    updated_at = models.DateTimeField()

    class Meta:
        db_table = 'ov\".\"znizenie_imania_ceos'