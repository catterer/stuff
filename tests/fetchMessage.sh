echo "
{
    \"method\" : \"fetchMessage\",
    \"userId\" : \"$1\"
}" | ./reqsend.sh
