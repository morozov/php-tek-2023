#!/usr/bin/env sh

for driver in pgsql oci8; do
    echo "Running E01LastInsertId with $driver"
    bin/run-example.php E01LastInsertId "$driver"
    echo
done

for driver in mysqli pdo_mysql pdo_pgsql; do
    echo "Running E02BlobMemoryUsageWithOrm with $driver"
    bin/run-example.php E02BlobMemoryUsageWithOrm "$driver"
    echo
done

for example in E02BlobMemoryUsageWithNativePgSql; do
    echo "Running E02BlobMemoryUsageWithNativePgSql"
    bin/run-example.php E02BlobMemoryUsageWithNativePgSql
    echo
done
