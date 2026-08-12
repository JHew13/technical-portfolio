# Technical Portfolio

A collection of sanitized Python, SQL, and PHP examples from internal business tools, automation, data analysis, and troubleshooting work I developed.

> **Public portfolio note:** The source code in this repository is based on code I originally wrote. Proprietary company names, database/schema names, internal URLs, credentials, identifiers, and business-specific sample data have been removed or replaced with generic equivalents. The original systems are not public, so these files are intended to demonstrate code structure, technical problem-solving, and implementation patterns rather than function as standalone production applications.

## Python

### `active_directory_user_export.py`
Retrieves user information from Active Directory/LDAP and transforms the results into structured data for reporting. Demonstrates directory integration, database connectivity, pandas-based transformation, and automated collection.

### `ruleset_etl_columns.py`
Automates a dynamic web application, searches large ruleset grids for selected values, handles dynamically loaded rows, and exports matches. Demonstrates Selenium automation, stale-element recovery, data validation, and Excel output.

### `ruleset_etl_excel.py`
Reads ruleset source files, transforms them with pandas, and loads refreshed tables into SQL Server. Demonstrates ETL-style ingestion, file processing, transformation, and database loading.

### `sentence_similarity_analysis.py`
Compares workflow questions to identify exact and near-duplicate wording. Demonstrates similarity analysis, permutations, pandas data preparation, and data-quality troubleshooting.

## SQL

### `linked_workflow_search.sql`
Traces parent/linked workflow relationships and can reverse the search direction to see where a workflow is referenced. Demonstrates complex joins, latest-revision logic, and relational troubleshooting.

### `workflow_by_review_type.sql`
Analyzes workflow relationships by review type while limiting results to current revisions. Demonstrates multi-table joins, subqueries, filtering, and versioned relational data.

### `workflow_component_search.sql`
Identifies current workflows containing a selected application component and traces related configuration. Demonstrates component/configuration analysis and complex relational joins.

## PHP

### `audit_dates_agent_view.php`
Retrieves audit-related records for a web interface. Demonstrates PHP backend processing, SQL integration, and dynamic data delivery.

### `fax_error_tracker.php`
Database-backed application for capturing, tracking, and searching operational errors. Demonstrates PHP/MySQL integration, validation, authenticated-user tracking, AJAX-style lookups, and audit fields.

### `supervisor_agent_dashboard.php`
Database-driven supervisor dashboard for viewing individual employee operational data. Demonstrates PHP, SQL-driven reporting, filtering, and dynamic web presentation.

## Technologies Demonstrated

**Python:** pandas, Selenium, openpyxl, SQLAlchemy, LDAP/Active Directory  
**Data:** SQL/T-SQL, relational joins, ETL-style processing, validation, reconciliation  
**Web:** PHP, MySQL, JavaScript/AJAX, HTML/CSS  
**Automation:** browser automation, file ingestion, Excel output, directory data extraction
