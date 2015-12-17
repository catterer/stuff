echo "
{
    \"method\" : \"createUser\",
    \"name\" : \"$1\"
}" | ./reqsend.sh
